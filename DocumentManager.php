<?php
/**
 * CrocoDoc Processor
 */
require_once 'crocodoc/Crocodoc.php';

class DocumentManager {
	
	var $uuids = array();
	
	/*
	 * Upload File
	 * 
	 * Upload a local file to Crocodoc
	 * @returns mixed uuid of uploaded file or boolean false
	 */
	public function uploadFile($filePath) {
		Crocodoc::setApiToken(CROCODOC_KEY);
		
		if (is_file($filePath)) {	
			$fileHandle = fopen($filePath, 'r');
			$uuid = null;
			try {
				$uuid = CrocodocDocument::upload($fileHandle);
				return $uuid;
			} catch (CrocodocException $e) {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/*
	 * Check Status
	 * 
	 * Check the status a file conversion
	 * @returns array of document details
	 */
	public function checkStatus($uuid) {
		Crocodoc::setApiToken(CROCODOC_KEY);
		
		$doc['uuid'] = $uuid;
		try {
			$status = CrocodocDocument::status($uuid);
		
			if (empty($status['error'])) {
				$doc['status'] = $status['status'];
				$doc['viewable'] = $status['viewable'] ? 'true' : 'false';
				$doc['message'] = 'success';
			} else {
				$doc['status'] = 'error';
				$doc['viewable'] = 'false';
				$doc['message'] = $status['error'];
			}
		} catch (CrocodocException $e) {
			$doc['status'] = 'error';
			$doc['viewable'] = 'false';
			$doc['message'] = $e->errorCode.' - '.$e->getMessage();
		}
		
		return $doc;
	}

	/*
	 * Check Statuses
	 * 
	 * Check the statuses of the files this instance has created
	 * @returns array of document details
	 */
	public function checkStatuses($uuids) {
		Crocodoc::setApiToken(CROCODOC_KEY);
		try {
			$statuses = CrocodocDocument::status($uuids);
			$count=0;
			foreach($statuses as $status) {
				
				$doc['uuid'] = $status['uuid'];
				if (empty($status['error'])) {
					$doc[$count]['status'] = $status['status'];
					$doc[$count]['viewable'] = $status['viewable'] ? 'true' : 'false';
					$doc[$count]['message'] = 'success';
				} else {
					$doc[$count]['status'] = 'error';
					$doc[$count]['viewable'] = 'false';
					$doc[$count]['message'] = $status['error'];
				}
				$count++;
			}
		} catch (CrocodocException $e) {
			$doc['status'] = 'error';
			$doc['viewable'] = 'false';
			$doc['message'] = $e->errorCode.' - '.$e->getMessage();
		}
		
		return $doc;
	}
	
	/*
	 * Delete File
	 * 
	 * Delete a file
	 * @returns boolean
	 */
	public function deleteFile($uuid) {
		Crocodoc::setApiToken(CROCODOC_KEY);
		try {
			$deleted = CrocodocDocument::delete($uuid);
			if ($deleted) {
				return true;
			} else {
				return false;
			}
		} catch (CrocodocException $e) {
			return false;
		}
		
	}
	
	/*
	 * Download File
	 * 
	 * Download a file to the browser
	 * @returns mixed
	 */
	public function downloadFile($uuid, $filename) {
		Crocodoc::setApiToken(CROCODOC_KEY);
		try {
			$data = CrocodocDownload::document($uuid, true); #file data. 

			header("Content-Type: application/pdf; name=".$filename);
			header("Content-Disposition: attachment; filename=".$filename);
			return $data;
			
		} catch (CrocodocException $e) {
			return false;
		}
	}
	
	/*
	 * Download Thumbnail
	 * 
	 * Download a file to the browser
	 * @returns mixed
	 */
	public function getThumbnail($uuid, $width=300, $height=300) {
		Crocodoc::setApiToken(CROCODOC_KEY);
		try {
			$data = CrocodocDownload::thumbnail($uuid, $width, $height); 
			return $data;
		} catch (CrocodocException $e) {
			return false;
		}
	}
	
	/*
	 * View Document
	 * 
	 * Download a file to the browser
	 * @returns mixed
	 */
	public function createSession($uuid) {
		Crocodoc::setApiToken(CROCODOC_KEY);
		$sessionKey = null;

		try {
			$sessionKey = CrocodocSession::create($uuid, array(
				'isEditable' => false,
				'filter' => 'none',
				'isAdmin' => false,
				'isDownloadable' => false,
				'isCopyprotected' => true,
				'isDemo' => false,
				'sidebar' => 'none'
			));
			return $sessionKey;
		} catch (CrocodocException $e) {
			return false;
		}

	}
	

} //end of DocumentManager class

