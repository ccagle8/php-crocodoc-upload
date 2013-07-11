<?php
/*
 * PHP Crocodoc Upload - Thumbnail Viewer
 * 
 * GitHub Repo: https://github.com/ccagle8/php-crocodoc-upload
 * 
 * @created 06/10/2013
 * @author Chris Cagle <admin@cagintranet.com>
 * 
 */
include('functions.php');


$cache_expire = 60*60*24*30;
header("Pragma: public");
header("Cache-Control: max-age=".$cache_expire);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');


$uuid = $_GET['uuid']; //pass in the uuid of the document 
$sql = "SELECT thumbnail FROM documents WHERE uuid='".clean($uuid)."'";
$data = mysql_fetch_assoc(mysql_query($sql));
$data = base64_decode(substr($data['thumbnail'], 23), true);
$image = imagecreatefromstring($data);
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);