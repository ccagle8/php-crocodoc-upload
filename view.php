<?php
/*
 * PHP Crocodoc Upload - Viewer
 * 
 * GitHub Repo: https://github.com/ccagle8/php-crocodoc-upload
 * 
 * @created 06/10/2013
 * @author Chris Cagle <admin@cagintranet.com>
 * 
 */
 
include('functions.php');
include('DocumentManager.php');
$message = null;

# This should be coming from a DB query to grab the UUID of the document you want to be viewing. 
$uuid = 'the_uuid_you_got_and_saved_during_the_upload';

# Get viewable file session
$md = new DocumentManager;
try {
	$sessionkey = $md->createSession($uuid);
	$fileview = 'https://crocodoc.com/view/'.$sessionkey;
} catch(Exception $e) {
	$message = 'There is a problem loading the document...';
}


?>
<!doctype html>
<html lang="en">
  <head>
		<meta charset="utf-8">
		<title>Crocodoc Upload Demo - Viewer</title>
	</head>
  <body>
		<?php echo $message; ?>
		
		<!-- DOCUMENT VIEWER -->
		<h3>Document Viewer</h3>
		<iframe src="<?php echo $fileview; ?>" height="500" width="400" ></iframe>
		
		
		<!-- DOCUMENT THUMBNAIL -->
		<h3>Document Thumbnail</h3>
		<p><img src="thumbnail?id=<?php echo $uuid; ?> " /></p>

	</body>
</html>