<?php
/*
 * PHP Crocodoc Upload - Functions & Config
 * 
 * GitHub Repo: https://github.com/ccagle8/php-crocodoc-upload
 * 
 * @created 06/10/2013
 * @author Chris Cagle <admin@cagintranet.com>
 * 
 */

# Get it by signing up at crocodoc.com
define('CROCODOC_KEY', 'PU8K3Gx2jqaTvur4eJihSnRf'); 



/*
 * NOTE! This page assumes that you have a live connection to a MySQL database. 
 * Either include your connection file, or create the connection now...
 */



/**
 * getDataURI 
 *
 * @param string $image, image data
 * @param string $mime, mime type, default null
 * @return string, image data uri
 */
function getDataURI($image, $mime = '') {
  return 'data: '.(function_exists('mime_content_type') ? mime_content_type($image) : $mime).';base64,'.base64_encode(file_get_contents($image));
}

/**
 * Clean Input for use in MySQL Statement 
 *
 * @param string $data
 * @param string $db, for use in a DB, almost always yes
 * @param string $html, for strip_tags
 * @return string 
 */
function clean($data, $db=true, $html=false) {
	
	$data = trim($data);
  if (get_magic_quotes_gpc()) { $data = stripslashes($data); } // if get magic quotes is on, stripslashes
  if ($html) { $data = strip_tags($data); } // no html wanted
	
	if (!$db) { // not used in query (just email or display)
		return $data;
	} elseif ($db) { // used in mysql query
		if (is_numeric($data)) {
			return $data;
		} else {
			$data = mysql_real_escape_string($data);
			return $data;
		}
 }
 
}