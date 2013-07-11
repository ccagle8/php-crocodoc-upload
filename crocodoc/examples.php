<?php
/**
 * Bootstrap
 */
error_reporting(E_ALL);
$exampleApiToken = 'YOUR_API_TOKEN';

// set the content type to plaintext if we're running this from a web browser
if (php_sapi_name() != 'cli') {
	header('Content-Type: text/plain');
}

require_once 'Crocodoc.php';
Crocodoc::setApiToken($exampleApiToken);

/*
 * Example #1
 * 
 * Upload a file to Crocodoc. We're uploading Form W4 from the IRS by URL.
 */
echo 'Example #1 - Upload Form W4 from the IRS by URL.' . "\n";
$formW4Url = 'http://www.irs.gov/pub/irs-pdf/fw4.pdf';
echo '  Uploading... ';
$uuid = null;

try {
	$uuid = CrocodocDocument::upload($formW4Url);
	echo 'success :)' . "\n";
	echo '  UUID is ' . $uuid . "\n";
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #2
 * 
 * Check the status of the file from Example #1.
 */
echo "\n";
echo 'Example #2 - Check the status of the file we just uploaded.' . "\n";
echo '  Checking status... ';

try {
	$status = CrocodocDocument::status($uuid);

	if (empty($status['error'])) {
		echo 'success :)' . "\n";
		echo '  File status is ' . $status['status'] . '.' . "\n";
		echo '  File ' . ($status['viewable'] ? 'is' : 'is not') . ' viewable.' . "\n";
	} else {
		echo 'failed :(' . "\n";
		echo '  Error Message: ' . $status['error'] . "\n";
	}
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #3
 * 
 * Upload another file to Crocodoc. We're uploading Form W4 from the IRS as a PDF.
 */
echo "\n";
echo 'Example #3 - Upload a sample .pdf as a file.' . "\n";
$uuid2 = null;
$filePath = dirname(__FILE__) . '/example-files/form-w4.pdf';

if (is_file($filePath)) {	
	$fileHandle = fopen($filePath, 'r');
	echo '  Uploading... ';
	$uuid2 = null;
	
	try {
		$uuid2 = CrocodocDocument::upload($fileHandle);
		echo 'success :)' . "\n";
		echo '  UUID is ' . $uuid2 . "\n";
	} catch (CrocodocException $e) {
		echo 'failed :(' . "\n";
		echo '  Error Code: ' . $e->errorCode . "\n";
		echo '  Error Message: ' . $e->getMessage() . "\n";
	}
} else {
	echo '  Skipping because the sample pdf can\'t be found.' . "\n";
}

/*
 * Example #4
 * 
 * Check the status of both files we uploaded in Examples #1 and #3.
 */
echo "\n";
echo 'Example #4 - Check the status of both files at the same time.' . "\n";
echo '  Checking statuses... ';

try {
	$statuses = CrocodocDocument::status(array($uuid, $uuid2));

	if (!empty($statuses)) {
		echo 'success :)' . "\n";
		
		if (empty($statuses[0]['error'])) {
			echo '  File #1 status is ' . $statuses[0]['status'] . '.' . "\n";
			echo '  File #1 ' . ($statuses[0]['viewable'] ? 'is' : 'is not') . ' viewable.' . "\n";
		} else {
			echo '  File #1 failed :(' . "\n";
			echo '  Error Message: ' . $statuses[0]['error'] . "\n";
		}
		
		if (empty($statuses[1]['error'])) {
			echo '  File #2 status is ' . $statuses[1]['status'] . '.' . "\n";
			echo '  File #2 ' . ($statuses[1]['viewable'] ? 'is' : 'is not') . ' viewable.' . "\n";
		} else {
			echo '  File #2 failed :(' . "\n";
			echo '  Error Message: ' . $statuses[1]['error'] . "\n";
		}
	} else {
		echo 'failed :(' . "\n";
		echo '  Statuses were not returned.' . "\n";
	}
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #5
 * 
 * Wait ten seconds and check the status of both files again.
 */
echo "\n";
echo 'Example #5 - Wait ten seconds and check the statuses again.' . "\n";
echo '  Waiting... ';
sleep(10);
echo 'done.' . "\n";
echo '  Checking statuses... ';

try {
	$statuses = CrocodocDocument::status(array($uuid, $uuid2));

	if (!empty($statuses)) {
		echo 'success :)' . "\n";
		
		if (empty($statuses[0]['error'])) {
			echo '  File #1 status is ' . $statuses[0]['status'] . '.' . "\n";
			echo '  File #1 ' . ($statuses[0]['viewable'] ? 'is' : 'is not') . ' viewable.' . "\n";
		} else {
			echo '  File #1 failed :(' . "\n";
			echo '  Error Message: ' . $statuses[0]['error'] . "\n";
		}
		
		if (empty($statuses[1]['error'])) {
			echo '  File #2 status is ' . $statuses[1]['status'] . '.' . "\n";
			echo '  File #2 ' . ($statuses[1]['viewable'] ? 'is' : 'is not') . ' viewable.' . "\n";
		} else {
			echo '  File #1 failed :(' . "\n";
			echo '  Error Message: ' . $statuses[1]['error'] . "\n";
		}
	} else {
		echo 'failed :(' . "\n";
		echo '  Statuses were not returned.' . "\n";
	}
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #6
 * 
 * Delete the file we uploaded from Example #1.
 */
echo "\n";
echo 'Example #6 - Delete the first file we uploaded.' . "\n";
echo '  Deleting... ';

try {
	$deleted = CrocodocDocument::delete($uuid);

	if ($deleted) {
		echo 'success :)' . "\n";
		echo '  File was deleted.' . "\n";
	} else {
		echo 'failed :(' . "\n";
	}
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #7
 * 
 * Download the file we uploaded from Example #3 as an original
 */
echo "\n";
echo 'Example #7 - Download a file as an original.' . "\n";
echo '  Downloading... ';

try {
	$file = CrocodocDownload::document($uuid2);
	$filename = dirname(__FILE__) . '/example-files/test-original.pdf';
	$fileHandle = fopen($filename, 'w');
	fwrite($fileHandle, $file);
	fclose($fileHandle);
	echo 'success :)' . "\n";
	echo '  File was downloaded to ' . $filename . '.' . "\n";
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #8
 * 
 * Download the file we uploaded from Example #3 as a PDF
 */
echo "\n";
echo 'Example #8 - Download a file as a PDF.' . "\n";
echo '  Downloading... ';

try {
	$file = CrocodocDownload::document($uuid2, true);
	$filename = dirname(__FILE__) . '/example-files/test.pdf';
	$fileHandle = fopen($filename, 'w');
	fwrite($fileHandle, $file);
	fclose($fileHandle);
	echo 'success :)' . "\n";
	echo '  File was downloaded to ' . $filename . '.' . "\n";
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #9
 * 
 * Download the file we uploaded from Example #3 with all options
 */
echo "\n";
echo 'Example #9 - Download a file with all options.' . "\n";
echo '  Downloading... ';

try {
	$file = CrocodocDownload::document($uuid2, true, true, 'all');
	$filename = dirname(__FILE__) . '/example-files/test-with-options.pdf';
	$fileHandle = fopen($filename, 'w');
	fwrite($fileHandle, $file);
	fclose($fileHandle);
	echo 'success :)' . "\n";
	echo '  File was downloaded to ' . $filename . '.' . "\n";
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #10
 * 
 * Download the file we uploaded from Example #3 as a default thumbnail
 */
echo "\n";
echo 'Example #10 - Download a default thumbnail from a file.' . "\n";
echo '  Downloading... ';

try {
	$file = CrocodocDownload::thumbnail($uuid2);
	$filename = dirname(__FILE__) . '/example-files/thumbnail.png';
	$fileHandle = fopen($filename, 'w');
	fwrite($fileHandle, $file);
	fclose($fileHandle);
	echo 'success :)' . "\n";
	echo '  File was downloaded to ' . $filename . '.' . "\n";
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #11
 * 
 * Download the file we uploaded from Example #3 as a large thumbnail
 */
echo "\n";
echo 'Example #11 - Download a large thumbnail from a file.' . "\n";
echo '  Downloading... ';

try {
	$file = CrocodocDownload::thumbnail($uuid2, 250, 250);
	$filename = dirname(__FILE__) . '/example-files/thumbnail-large.png';
	$fileHandle = fopen($filename, 'w');
	fwrite($fileHandle, $file);
	fclose($fileHandle);
	echo 'success :)' . "\n";
	echo '  File was downloaded to ' . $filename . '.' . "\n";
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #12
 * 
 * Download extracted text from the file we uploaded from Example #3
 */
echo "\n";
echo 'Example #12 - Download extracted text from a file.' . "\n";
echo '  Downloading... ';

try {
	$file = CrocodocDownload::text($uuid2);
	$filename = dirname(__FILE__) . '/example-files/text.txt';
	$fileHandle = fopen($filename, 'w');
	fwrite($fileHandle, $file);
	fclose($fileHandle);
	echo 'success :)' . "\n";
	echo '  File was downloaded to ' . $filename . '.' . "\n";
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #13
 * 
 * Create a session key for the file we uploaded from Example #3 with default
 * options.
 */
echo "\n";
echo 'Example #13 - Create a session key for a file with default options.' . "\n";
echo '  Creating... ';
$sessionKey = null;

try {
	$sessionKey = CrocodocSession::create($uuid2);
	echo 'success :)' . "\n";
	echo '  The session key is ' . $sessionKey . '.' . "\n";
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #14
 * 
 * Create a session key for the file we uploaded from Example #3 all of the
 * options.
 */
echo "\n";
echo 'Example #14 - Create a session key for a file with all of the options.' . "\n";
echo '  Creating... ';
$sessionKey = null;

try {
	$sessionKey = CrocodocSession::create($uuid2, array(
		'isEditable' => true,
		'user' => array(
			'id' => 1,
			'name' => 'John Crocodile',
		),
		'filter' => 'all',
		'isAdmin' => true,
		'isDownloadable' => true,
		'isCopyprotected' => false,
		'isDemo' => false,
		'sidebar' => 'visible',
	));
	echo 'success :)' . "\n";
	echo '  The session key is ' . $sessionKey . '.' . "\n";
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}

/*
 * Example #15
 * 
 * Delete the file we uploaded from Example #2.
 */
echo "\n";
echo 'Example #15 - Delete the second file we uploaded.' . "\n";
echo '  Deleting... ';

try {
	$deleted = CrocodocDocument::delete($uuid2);

	if ($deleted) {
		echo 'success :)' . "\n";
		echo '  File was deleted.' . "\n";
	} else {
		echo 'failed :(' . "\n";
	}
} catch (CrocodocException $e) {
	echo 'failed :(' . "\n";
	echo '  Error Code: ' . $e->errorCode . "\n";
	echo '  Error Message: ' . $e->getMessage() . "\n";
}
