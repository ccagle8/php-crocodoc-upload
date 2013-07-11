<?php
require_once 'Crocodoc.php';

/**
 * Provides access to the Crocodoc Session API. The Session API is used to
 * to create sessions for specific documents that can be used to view a
 * document using a specific session-based URL.
 */
class CrocodocSession extends Crocodoc {
	/**
	 * The Download API path relative to the base API path
	 * 
	 * @var string
	 */
	public static $path = '/session/';
	
	/**
	 * Create a session for a specific document by UUID that is optionally
	 * editable and can use user ID and name info from your application,
	 * can filter annotations, can grant admin permissions, can be
	 * downloadable, can be copy-protected, and can prevent changes from being
	 * persisted.
	 * 
	 * @param string $uuid The uuid of the file to create a session for
	 * @param array $params An associative array representing:
	 *   bool 'isEditable' Can users create annotations and comments while
	 *     viewing the document with this session key?
	 *   array 'user' An array with keys "id" and "name" representing
	 *     a user's unique ID and name in your application; "id" must be a
	 *     non-negative signed 32-bit integer; this field is required if
	 *     isEditable is true
	 *   string 'filter' Which annotations should be included if any - this
	 *     is usually a string, but could also be an array if it's a
	 *     comma-separated list of user IDs as the filter
	 *   bool 'isAdmin' Can users modify or delete any annotations or comments
	 *     belonging to other users?
	 *   bool 'isDownloadable' Can users download the original document?
	 *   bool 'isCopyprotected' Can text be selected in the document?
	 *   bool 'isDemo' Should we prevent any changes from being persisted?
	 *   string 'sidebar' Sets if and how the viewer sidebar is included
	 * 
	 * @return string A unique session key for the document
	 * @throws CrocodocException
	 */
	public static function create($uuid, $params = array()) {
		$postParams = array(
			'uuid' => $uuid,
		);
		
		if (isset($params['isEditable'])) {
			$postParams['editable'] = $params['isEditable'] ? 'true' : 'false';
		}
		
		if (
			!empty($params['user'])
			&& is_array($params['user'])
			&& isset($params['user']['id'])
			&& isset($params['user']['name'])
		) {
			$postParams['user'] = $params['user']['id'] . ',' . $params['user']['name'];
		}
		
		if (!empty($params['filter'])) {
			if (is_array($params['filter'])) {
				$params['filter'] = implode(',', $params['filter']);
			}
			
			$postParams['filter'] = $params['filter'];
		}
		
		if (isset($params['isAdmin'])) {
			$postParams['admin'] = $params['isAdmin'] ? 'true' : 'false';
		}
		
		if (isset($params['isDownloadable'])) {
			$postParams['downloadable'] = $params['isDownloadable'] ? 'true' : 'false';
		}
		
		if (isset($params['isCopyprotected'])) {
			$postParams['copyprotected'] = $params['isCopyprotected'] ? 'true' : 'false';
		}
		
		if (isset($params['isDemo'])) {
			$postParams['demo'] = $params['isDemo'] ? 'true' : 'false';
		}
		
		if (isset($params['sidebar'])) {
			$postParams['sidebar'] = $params['sidebar'];
		}
		
		$session = static::_request('create', null, $postParams);
		
		if (!is_array($session) || empty($session['session'])) {
			return static::_error('missing_session_key', __CLASS__, __FUNCTION__, $session);
		}
		
		return $session['session'];
	}
}
