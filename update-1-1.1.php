<?php
//-----------------------------------------------------------------------------
// NinjaLinks Copyright © Jem Turner 2007-2009 unless otherwise noted
// http://www.jemjabella.co.uk/
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License. See README.txt
// or LICENSE.txt for more information.
//-----------------------------------------------------------------------------

require('config.php');
include('header.php');

// convert db tables to utf8
$convertUTF8 = $mysql->query("ALTER DATABASE `".$dbname."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
if ($convertUTF8) {
	echo '<p>Database successfully converted to UTF8 for foreign chars.</p>';
	
	$convertBanned = $mysql->query("ALTER TABLE `".$dbpref."banned` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
	$convertCategories = $mysql->query("ALTER TABLE `".$dbpref."categories` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
	$convertLinks = $mysql->query("ALTER TABLE `".$dbpref."links` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
	$convertUpdates = $mysql->query("ALTER TABLE `".$dbpref."updates` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
	
	if ($convertBanned && $convertCategories &&	$convertLinks && $convertUpdates)
		echo '<p>NinjaLinks database tables charsets updated to UTF8.</p>';
	else
		echo '<p style="color: red;">Tables could not be converted to UTF8 - check database settings and try again.</p>';
} else
	echo '<p style="color: red;">Database could not be converted to UTF8 - check database settings and try again.</p>';


// add ownername field varchar(50)
$addName = $mysql->query("ALTER TABLE `".$dbpref."links` ADD `ownername` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
if ($addName)
	echo '<p>"ownername" field successfully added to links table.</p>';
else
	echo '<p style="color: red;">"ownername" field could not be added to links table - check database settings and try again.</p>';
	
// update linkname field to varchar(250)
$updateLinkname = $mysql->query("ALTER TABLE `".$dbpref."links` CHANGE `linkname` `linkname` VARCHAR(250)");
if ($updateLinkname)
	echo '<p>"linkname" field successfully updated to 250 allowed chars.</p>';
else
	echo '<p style="color: red;">"linkname" field could not updated - check database settings and try again.</p>';
?>
	
	<p>If there are no red errors above, consider this upgrade a success! :) You must now <b>delete update-1-1.1.php</b></p>

<?php	
include('footer.php');
?>