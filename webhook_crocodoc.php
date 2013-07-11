<?php
/*
 * PHP Crocodoc Upload - Webhook
 * 
 * GitHub Repo: https://github.com/ccagle8/php-crocodoc-upload
 * 
 * @created 06/10/2013
 * @author Chris Cagle <admin@cagintranet.com>
 *
 * Note: This webhook grabs the thumbnail that Crocodoc creates, 
 * then saves it in your database for viewing later.
 * 
 */
 

include('functions.php');
include('DocumentManager.php');
$message = null;


$payloads = json_decode(stripslashes($_POST['payload']));

if ($payloads) {
	$md = new DocumentManager;

	foreach($payloads as $payload) {
		if($payload->event == 'document.status' && $payload->status == 'DONE') {
			$filename = 'cache/'.(string)$payload->uuid.'.png';
			$data = $md->getThumbnail($payload->uuid);
			$status = file_put_contents($filename, $data);
			$data = getDataURI($filename);
			unlink($filename);
			if($data) {
				$sql = "UPDATE documents SET thumbnail='".clean($data)."' WHERE uuid = '".clean($payload->uuid)."' LIMIT 1";
				mysql_query($sql);
				echo $sql;
				# error
				if(mysql_affected_rows() == 0) {
					echo 'mysql error: '.mysql_error().'<br /><br />';
					
				} else {
					echo 'success!';
				}
			}
		} else {
			echo 'invalid status ('.$payload->status.') or event ('.$payload->event.'). ';
		}
	}
} else {
	echo 'invalid payload sent:<br />';
	print_r($_POST['payload']);
}
