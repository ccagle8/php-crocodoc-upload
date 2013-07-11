<?php
// require our exception class
require_once 'CrocodocException.php';

// require the different crocodoc clients
require_once 'CrocodocDocument.php';
require_once 'CrocodocDownload.php';
require_once 'CrocodocSession.php';

// borrowed from Facebook's API :)
// check for curl and json_decode
if (!function_exists('curl_init')) {
	throw new Exception('Crocodoc needs the CURL PHP extension.');
}

if (!function_exists('json_decode')) {
	throw new Exception('Crocodoc needs the JSON PHP extension.');
}

/**
 * Provides access to the Crocodoc API. This is a base class that can be used
 * standalone with full access to the other Crocodoc API clients (Document,
 * Download, and Session), and is also used internally by the other Crocodoc
 * API clients for generic methods including error and request.
 */
class Crocodoc {
	/**
	 * The developer's Crocodoc API token
	 * 
	 * @var string;	
	 */
	public static $apiToken;
	
	/**
	 * A good set of default curl options. Borrowed from Facebook's API :)
	 * 
	 * @var array
	 */
	public static $curlDefaultOptions = array(
		CURLOPT_CONNECTTIMEOUT => 10,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 60,
		CURLOPT_USERAGENT => 'crocodoc-php',
	);

	/**
	 * The default protocol (Crocodoc uses HTTPS)
	 * 
	 * @var string;	
	 */
	public static $protocol = 'https';
	
	/**
	 * The default host
	 * 
	 * @var string;	
	 */
	public static $host = 'crocodoc.com';
	
	/**
	 * The default base path on the server where the API lives
	 * 
	 * @var string;	
	 */
	public static $basePath = '/api/v2';
	
	/**
	 * An API path relative to the base API path
	 * 
	 * @var string
	 */
	public static $path = '/';
	
	/**
	 * Handle an error. We handle errors by throwing an exception.
	 * 
	 * @param string $error An error code representing the error
	 *   (use_underscore_separators)
	 * @param string $client Which API client the error is being called from
	 * @param string $method Which method the error is being called from
	 * @param array $response This is an array of the response, usually from JSON, but
	 *   can also be a string
	 * 
	 * @throws CrocodocException
	 */
	protected static function _error($error, $client, $method, $response) {
		$message = __CLASS__ . ': [' . $error . '] ' . $client . '::' . $method . "\r\n\r\n";
		
		if (is_array($response)) {
			$response = json_encode($response);
		}
		
		$message .= $response;
		$exception = new CrocodocException($message);
		$exception->errorCode = $error;
		throw $exception;
	}
	
	/**
	 * Make an HTTP request. Some of the params are polymorphic - getParams and
	 * postParams. 
	 * 
	 * @param string $method This is just an addition to the path, for example,
	 *   in "/documents/upload" the method would be "upload"
	 * @param array|string $getParams An array of GET params to be added to the
	 *   URL - this can also be a string
	 * @param array|string $postParams An array of GET params to be added to
	 *   the URL - this can also be a string
	 * @param bool isJson Should the file be converted from JSON? Defaults to
	 *   true.
	 * 
	 * @return array|string The response array is usually converted from JSON,
	 *   but sometimes we just return the raw response from the server
	 * @throws CrocodocException
	 */
	protected static function _request($method, $getParams, $postParams, $isJson = true) {
		$ch = curl_init();
		$url = static::$protocol . '://' . static::$host . static::$basePath . static::$path . $method;
		$options = static::$curlDefaultOptions;
		
		// if $getParams is a string, turn it into an array
		if (!empty($getParams) && is_string($getParams)) {
			$getParams = parse_str($getParams);
		}
		
		// add the API token to $getParams
		if (empty($getParams)) $getParams = array();
		$getParams['token'] = static::$apiToken;
		
		// turn $getParams into a query string and append it to $url
		if (!empty($getParams)) {
			$getParams = http_build_query($getParams, null, '&');
			$url .= (strpos($url, '?')) ? '&' : '?';
			$url .= $getParams;
		}
		
		// turn $postParams into a query string and add it to the curl $options
		if (!empty($postParams)) {
			// add the API token to $postParams
			if (is_string($postParams)) $postParams = parse_str($postParams);
			$postParams['token'] = static::$apiToken;
			
			foreach ($postParams as $key => $value) {
				if (is_resource($value)) {
					$metadata = stream_get_meta_data($value);
					
					if (empty($metadata) || empty($metadata['uri'])) {
						return static::_error('invalid_file_upload', __CLASS__, __FUNCTION__, $metadata);
					}
					
					$postParams[$key] = '@' . $metadata['uri'];
				}
			}
			
			$options[CURLOPT_POST] = 1;
			$options[CURLOPT_POSTFIELDS] = $postParams;
		}
		
		$options[CURLOPT_URL] = $url;
		
		// borrowed from Facebook's API :)
		// disable the 'Expect: 100-continue' behavior. This causes CURL to wait
		// for 2 seconds if the server does not support this header.
		if (empty($options[CURLOPT_HTTPHEADER])) $options[CURLOPT_HTTPHEADER] = array();
		$options[CURLOPT_HTTPHEADER][] = 'Expect:';
		
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		
		$curlErrno = curl_errno($ch);
		
		if (!empty($curlErrno)) {
			$curlError = curl_error($ch);
			curl_close($ch);
			return static::_error('curl_exception', __CLASS__, __FUNCTION__, array(
				'curl_errno' => $curlErrno,
				'curl_error' => $curlError,
			));
		}
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($isJson) {
			$jsonDecoded = @json_decode($result, true);
	
			if ($jsonDecoded === false || $jsonDecoded === null) {
				return static::_error('server_response_not_valid_json', __CLASS__, __FUNCTION__, array(
					'response' => $result,
					'getParams' => $getParams,
					'postParams' => $postParams,
				));
			}
			
			if (is_array($jsonDecoded) && !empty($jsonDecoded['error'])) {
				return static::_error($jsonDecoded['error'], __CLASS__, __FUNCTION__, array(
					'getParams' => $getParams,
					'postParams' => $postParams,
				));
			}
			
			$result = $jsonDecoded;
		}
		
		$http4xxErrorCodes = array(
			400 => 'bad_request',
			401 => 'unauthorized',
			404 => 'not_found',
			405 => 'method_not_allowed',
		);
		
		if (isset($http4xxErrorCodes[$httpCode])) {
			$error = 'server_error_' . $httpCode . '_' . $http4xxErrorCodes[$httpCode];
			return static::_error($error, __CLASS__, __FUNCTION__, array(
				'url' => $url,
				'getParams' => $getParams,
				'postParams' => $postParams,
			));
		}
		
		if ($httpCode >= 500 && $httpCode < 600) {
			$error = 'server_error_' . $httpCode . '_unknown';
			return static::_error($error, __CLASS__, __FUNCTION__, array(
				'url' => $url,
				'getParams' => $getParams,
				'postParams' => $postParams,
			));
		}
		
		return $result;
	}
	
	/**
	 * Get the API token
	 * 
	 * @return string The API token
	 */
	public static function getApiToken() {
		return static::$apiToken;
	}
	
	/**
	 * Set the API token
	 * 
	 * @param string $apiToken The API token
	 */
	public static function setApiToken($apiToken) {
		static::$apiToken = $apiToken;
	}
}
