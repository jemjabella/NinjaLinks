<?php
//-----------------------------------------------------------------------------
// NinjaLinks Copyright © Jem Turner 2007, 2008 unless otherwise noted
// http://www.jemjabella.co.uk/
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License. See README.txt
// or LICENSE.txt for more information.
//-----------------------------------------------------------------------------

include('header.php');

switch(getView()) {
case "delete":
	if (!isset($_POST['zomgkey']) || $_POST['zomgkey'] != md5($opt['salt'] . date("H")))
		exit('<p>Invalid token. <a href="manage_updates.php">Try again</a>?</p>');
	
	doDelete("updates", $_POST['del']);
break;
case "add":
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$error = NULL;

		if (!ereg("^[A-Za-z0-9\(\)_' -]*$", $_POST['title']))
			$error = "Title contains invalid characters, please fix and try again.";
		elseif (preg_match("/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/i", $_POST['title']))
			$error = "Title contains invalid characters. Please amend and try again.";
		
		if ($error == NULL) {
			foreach($_POST as $key => $value)
				if (isset($opt['cleanupdates']) && $opt['cleanupdates'] == 0)
					$$key = escape($value);
				else
					$$key = clean($value, 'yes');

			$addUpdate = $mysql->query("INSERT INTO `".$dbpref."updates` (`title`, `entry`, `datetime`) VALUES ('".$title."', '".$entry."', '". TODAY ."')");
			
			if ($addUpdate) {
				echo '<p><b class="red">Note:</b> The update was successfully added. <a href="manage_updates.php">Return to Manage Updates</a>.</p>';
			} else {
				echo '<p><b class="red">Note:</b> There was an error, the update could not be added. Please try again; or, contact your host for help if the MySQL connection has failed.</p>';
			}
		}
	}
	
	if (isset($error))
		echo '<p class="red">'.$error.'</p>';
	
?>
	<form action="manage_updates.php?v=add" method="post" id="linkform">
	<fieldset>
		<label for="title">Update Title</label>
		<input type="text" name="title" id="title" />
		
		<label for="entry">Entry</label>
		<textarea name="entry" id="entry" rows="10" cols="5"></textarea>
		
		<input type="submit" name="submit" class="button" value="Add Update" />
	</fieldset>
	</form>
<?php
break;
case "edit":
	if (!isset($_GET['id']) || !is_numeric($_GET['id']))
		exit('<p>Invalid category ID</p>');

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$error = NULL;

		// check the POSTed md5 hash of salt + link id against the hash of salt plus GET id (if the GET has been tampered with, will fail)
		if ($_POST['updid'] != md5($opt['salt'] . $_GET['id']))
			exit('<p>Update IDs do not match</p>');
		
		if (!ereg("^[A-Za-z0-9\(\)_' -]*$", $_POST['title']))
			$error = "Title contains invalid characters, please fix and try again.";

		if ($error == NULL) {
			foreach($_POST as $key => $value)
				if (isset($opt['cleanupdates']) && $opt['cleanupdates'] == 0)
					$$key = escape($value);
				else
					$$key = clean($value, 'yes');

			$editUpdate = $mysql->query("UPDATE `".$dbpref."updates` SET
				`title` = '".$title."',
				`entry` = '".$entry."'
			WHERE `id` = ".(int)$_GET['id']." LIMIT 1");
			
			if ($editUpdate) {
				echo '<p><b class="red">Note:</b> The update was successfully edited. <a href="manage_updates.php">Return to Manage Update</a>.</p>';
			} else {
				echo '<p><b class="red">Note:</b> There was an error, the update could not be edited. Please try again; or, contact your host for help if the MySQL connection has failed.</p>';
			}
		}
	}
	
	if (isset($error))
		echo '<p class="red">'.$error.'</p>';
	
	$getupdate = $mysql->query("SELECT * FROM `".$dbpref."updates` WHERE `id` = ".(int)$_GET['id']." LIMIT 1");
	if (mysql_num_rows($getupdate) == 1) {
		$up = mysql_fetch_assoc($getupdate);
?>
		<form action="manage_updates.php?v=edit&amp;id=<?php echo $up['id']; ?>" method="post" id="linkform">
		<fieldset>
			<input type="hidden" name="updid" id="updid" value="<?php echo md5($opt['salt'] . $up['id']); ?>" />

			<label for="title">Update Title</label>
			<input type="text" name="title" id="title" value="<?php echo $up['title']; ?>" />
			
			<label for="entry">Entry</label>
			<textarea name="entry" id="entry" rows="10" cols="5"><?php echo $up['entry']; ?></textarea>

			<input type="submit" name="submit" class="button" value="Edit Update" />
		</fieldset>
		</form>
<?php
	} else {
		echo "<p>Oh noes! There ain't no update to be edited with that ID, matey.</p>";
	}
break;
default:
?>
	<h1>Manage Updates</h1>
	<p><a href="?v=add">Add an Update</a></p>
	
<?php
	$from = ((getPage() * $opt['perpage']) - $opt['perpage']);

	$adminUpdates = $mysql->query("SELECT *, DATE_FORMAT(`datetime`, '%D %b %Y') AS `date` FROM `".$dbpref."updates` ORDER BY `date`, `id` DESC  LIMIT ".$from.", ".$opt['perpage']);
?>
	<form action="manage_updates.php?v=delete" method="post">
	<p>
		<input type="hidden" name="zomgkey" id="zomgkey" value="<?php echo md5($opt['salt'] . date("H")); ?>" />
	</p>
	<table>
	<tr><th>Title</th> <th>Update Snippet</th> <th>Date Added</th> <th colspan="2">Admin</th></tr>
<?php
	$rowCount = 0;
	while ($u = mysql_fetch_assoc($adminUpdates)) {
		if ($rowCount % 2) $rowClass = 'linkeven';
		else $rowClass = 'linkodd';

		echo '
			<tr class="'.$rowClass.'"><td>'.$u['title'].'</td> 
			<td>'.make_excerpt($u['entry'], 255, "...", true).'</td> <td>'.$u['date'].'</td> 
			
			<td><a href="manage_updates.php?v=edit&amp;id='.$u['id'].'"><img src="../njicons/edit.gif" title="edit this entry" alt="edit" /></a></td>
			<td class="center"><input type="checkbox" name="del['.$u['id'].']" value="'.$u['id'].'" /></td>

			</tr>'."\r\n";
		
		++$rowCount;
	}
?>
	</table>
	<p class="right"><input type="submit" name="submit" value="Delete" /></p>
	</form>
<?php
	getPagination($mysql->single("SELECT COUNT(`id`) FROM `".$dbpref."updates`"));
break;
}
include('../footer.php');
?>