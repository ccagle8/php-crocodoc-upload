<?php
require_once 'Crocodoc.php';

/**
 * Provides access to the Crocodoc Download API. The Download API is used for
 * downloading an original of a document, a PDF of a document, a thumbnail of a
 * document, and text extracted from a document.
 */
class CrocodocDownload extends Crocodoc {
	/**
	 * The Download API path relative to the base API path
	 * 
	 * @var string
	 */
	public static $path = '/download/';
	
	/**
	 * Download a document's original file from Crocodoc. The file can
	 * optionally be downloaded as a PDF, as another filename, with
	 * annotations, and with filtered annotations.
	 * 
	 * @param string $uuid The uuid of the file to download
	 * @param bool $isPdf Should the file be downloaded as a PDF?
	 * @param bool $isAnnotated Should the file be downloaded with annotations?
	 * @param string|string[] $filter Which annotations should be included if
	 *   any - this is usually a string, but could also be an array if it's a
	 *   comma-separated list of user IDs as the filter
	 * 
	 * @return string The downloaded file contents as a string
	 * @throws CrocodocException
	 */
	public static function document($uuid, $isPdf = false, $isAnnotated = false, $filter = null) {
		$getParams = array(
			'uuid' => $uuid,
		);
		if ($isPdf) $getParams['pdf'] = 'true';
		if ($isAnnotated) $getParams['annotated'] = 'true';
		
		if ($filter) {
			if (is_array($filter)) $filter = implode(',', $filter);
			$getParams['filter'] = $filter;
		}
		
		return static::_request('document', $getParams, null, false);
	}
	
	/**
	 * Download a document's extracted text from Crocodoc.
	 * 
	 * @param string $uuid The uuid of the file to extract text from
	 * 
	 * @return string The file's extracted text
	 * @throws CrocodocException
	 */
	public static function text($uuid) {
		$getParams = array(
			'uuid' => $uuid,
		);
		return static::_request('text', $getParams, null, false);
	}
	
	/**
	 * Download a document's thumbnail from Crocodoc with an optional size.
	 * 
	 * @param string $uuid The uuid of the file to download the thumbnail from
	 * @param int $width The width you want the thumbnail to be
	 * @param int $height The height you want the thumbnail to be
	 * 
	 * @return string The downloaded thumbnail contents
	 * @throws CrocodocException
	 */
	public static function thumbnail($uuid, $width = null, $height = null) {
		$getParams = array(
			'uuid' => $uuid,
		);
		
		if (!empty($width) && !empty($height)) {
			$getParams['size'] = $width . 'x' . $height;
		}

		return static::_request('thumbnail', $getParams, null, false);
	}
}
