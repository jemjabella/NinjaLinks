<?php
//-----------------------------------------------------------------------------
// NinjaLinks Copyright © Jem Turner 2007-2009 unless otherwise noted
// http://www.jemjabella.co.uk/
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License. See README.txt
// or LICENSE.txt for more information.
//-----------------------------------------------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="stylesheet.css" rel="stylesheet" type="text/css" />

<title><?php echo $opt['dirname']; ?></title>

</head>
<body>

<div id="container">

<?php 
	if (basename($_SERVER['SCRIPT_NAME']) != "install.php")
		checkInstall(); 
?>

	<ul id="navigation">
		<li><a href="<?php echo $opt['dirlink']; ?>">Home</a></li>
		<li><a href="addlink.php">Add Link</a></li>
		<li><a href="links.php">View Links</a></li>
		<li><a href="contact.php">Contact</a></li>
	</ul>