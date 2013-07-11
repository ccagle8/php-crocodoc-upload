<?php
/*
 * PHP Crocodoc Upload - Uploader
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



if (isset($_POST['submit'])) {
	
	/*
	 * Validate the file type is valid. Crocodoc accepts more filetypes than this, 
	 * but this should be a good start for you to do at least some some validation.
	 */
	if ($_FILES['file']['error'] == 0) {
		$extention = strtolower(end(explode(".", $_FILES["file"]["name"])));
		if ($extention != 'pdf' && $extention != 'docx' && $extention != 'doc' && $extention != 'pptx' && $extention != 'ppt' ){
			$message = 'Please upload a valid full file type before continuing.';
		}
	}
	
	
	# Save to db
	if (!$message) {

		if ($_FILES['file']['error'] == 0) {
			
			# Grab the document from $_FILES
			$tempfile = $_FILES["file"]["tmp_name"];
			$full_filename = md5($hash_id).'.'.$extention;
			move_uploaded_file($_FILES['file']['tmp_name'], 'documents/'.$full_filename);
			
			# Upoad to Crocodoc API
			$md = new DocumentManager;
			$uuid = $md->uploadFile('documents/'.$full_filename);
			
			# Check response from Crocodoc
			if (!$uuid) {
				$message = 'There was a problem uploading the document to Crocodoc.';
			} else {
				
				# Save it in the DB for future reference
				$sql = "INSERT INTO documents (uuid, document) VALUES ('".clean($uuid)."', '".clean($full_filename)."')";
				$status = mysql_query($sql);
				if (mysql_error()) {
					$message = 'There was a problem saving the document in the SQL db: <code>'.mysql_error().'</code>';
				}
			}

		}

		# If no errors, say 'Yaaaaaaa'
		if (!$message) {
			$message = 'The file was successfully uploaded!';
		}
		
		
	}
}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Crocodoc Upload Demo</title>
	</head>
	<body>
		
		<?php echo $message; ?>
		<form action="" method="post" enctype="multipart/form-data" >
			<p>
				<label>Document: <input name="file" type="file" required ></label>
				<em>Only accepts .PDF, .DOC, .DOCX, .PPT & .PPTX filetypes.</em>
			</p>
			<p><button name="submit" >Upload</button></p>
		</form>
		
	</body>
</html>