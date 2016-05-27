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

// empty categories first to get rid of default ones
$mysql->query("TRUNCATE TABLE `".$dbpref."categories`");
// empty links - should be empty anyway
$mysql->query("TRUNCATE TABLE `".$dbpref."links`");

$simple_dbhost = "localhost";   // this can usually be left as localhost
$simple_dbuser = "username";   // database username
$simple_dbpass = "password";   // database user password
$simple_dbname = "database";   // the name of the database

$simple_links = "SD_links";   // name of the links table
$simple_cats = "SD_cats";   // name of the categories table


if (empty($dbuser) || empty($dbpass) || empty($dbname))
	exit("You must fill out the username, password and database name fields in config.php");
elseif (empty($simple_dbuser) || empty($simple_dbpass) || empty($simple_dbname))
	exit("You must fill out the SimpleDir username, password and database name fields in convert_from_simpledir.php");

$simplesql = new mysql($simple_dbhost, $simple_dbuser, $simple_dbpass, $simple_dbname);

$getCats = $simplesql->query("SELECT * FROM `".$simple_cats."`");
$catQry = array();
while($c = mysql_fetch_assoc($getCats))
	$catQry[] = "('".$c['catID']."', '".$c['catname']."')";
	
$buildCatQry = "INSERT INTO `".$dbpref."categories` (`id`, `catname`) VALUES ".implode(", ", $catQry);

$insertNinjaCats = $mysql->query($buildCatQry);
if ($insertNinjaCats)
	echo '<p>Successfully imported categories.</p>';
else
	echo '<p style="color: red;">Categories could not be imported - check database settings and try again.</p>';


$getLinks = $simplesql->query("SELECT * FROM `".$simple_links."`");
$linkQry = array();
while($l = mysql_fetch_assoc($getLinks))
	$linkQry[] = "('".$l['owneremail']."', '".$l['linkname']."', '".$l['linkurl']."', '".$l['linkdesc']."', '".$l['relCatID']."', '".$l['linkstatus']."', '". TODAY ."')";
	
$buildLinkQry = "INSERT INTO `".$dbpref."links` (`owneremail`, `linkname`, `linkurl`, `linkdesc`, `category`, `approved`, `dateupdated`) VALUES ".implode(", ", $linkQry);

$insertNinjaLinks = $mysql->query($buildLinkQry);
if ($insertNinjaLinks)
	echo '<p>Successfully imported links.</p>';
else
	echo '<p style="color: red;">Links could not be imported - check database settings and try again.</p>';

include('footer.php');
?>