<?php
include('config.php');
if (isset($_GET['link']) && is_numeric($_GET['link'])) {
	$link = $mysql->query("SELECT `linkurl` FROM `".$dbpref."links` WHERE `id` = ".(int)$_GET['link']." LIMIT 1");
	if (mysql_num_rows($link) == 1) {
		$link = mysql_fetch_assoc($link);
		$mysql->single("UPDATE `".$dbpref."links` SET `hits` = `hits` + 1 WHERE `id` = ".(int)$_GET['link']." LIMIT 1");
		header("Status: 301");
		header("Location: ".$link['linkurl']);
		exit('Could not forward, <a href="'.$link['linkurl'].'">click here to continue</a>');
	} else {
		exit("Invalid link ID.");
	}
} else {
	exit("Invalid link ID.");
}
?>