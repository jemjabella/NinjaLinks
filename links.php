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

if (isset($_GET['cat']) && is_numeric($_GET['cat'])) {
	$getCat = $mysql->single("SELECT `catname` FROM `".$dbpref."categories` WHERE `id` = ".(int)$_GET['cat']." LIMIT 1");
	if ($getCat) {
?>
		<h1>Viewing Category &raquo; <?php echo $getCat; ?></h1>
<?php	
		$checkLinks = $mysql->single("SELECT COUNT(`id`) FROM `".$dbpref."links` WHERE `category` = ".(int)$_GET['cat']." AND `approved` = 1");
		if ($checkLinks > 0) {
			$from = ((getPage() * $opt['perpage']) - $opt['perpage']);

			getLinks($from, $opt['perpage'], $_GET['cat']);
			
			getPagination($checkLinks);
		} else {
			echo '<p>There are no approved links in this category.</p>';
		}
?>
		<p><a href="links.php">Return to category listing</a></p>
<?php
		include('footer.php');
		exit;
	}
}
?>

<h1>Links</h1>

<ul>
<?php getAllCats('list', '&nbsp;&nbsp;&raquo; '); ?>
</ul>



<?php
include('footer.php');
?>