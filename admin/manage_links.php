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
	if (!isset($_POST['smashyhashy']) || $_POST['smashyhashy'] != md5($opt['salt'] . date("H")))
		exit('<p>Invalid token. <a href="manage_links.php">Try again</a>?</p>');
	
	doDelete("links", $_POST['del']);
break;
case "approve":
case "edit":
	if (!isset($_GET['id']) || !is_numeric($_GET['id']))
		exit('<p>Invalid link ID</p>');

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$error = NULL;

		// check the POSTed md5 hash of salt + link id against the hash of salt plus GET id (if the GET has been tampered with, will fail)
		if ($_POST['linkid'] != md5($opt['salt'] . $_GET['id']))
			exit('<p>Link IDs do not match</p>');
		
		foreach($_POST as $key => $value)
			if (in_array($key, $opt['required']) && empty($value))
				$error = $key.' is a required field.';
		
		if (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$", strtolower($_POST['email'])))
			$error = "Invalid E-mail Address, please fix and try again.";
		elseif (!preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $_POST['linkurl']))
			$error = "Invalid Link URL, please fix and try again.";
		elseif (!is_numeric($_POST['linkcat']))
			$error = "Invalid Category, please fix and try again.";
		elseif (!is_numeric($_POST['approve']))
			$error = "Invalid 'approve' option, please fix and try again.";
			
		if ($opt['allowbutton'] == 0 && !empty($_POST['linkbutton']))
			$error = "Button URL shouldn't be filled in!";
		
		if ($opt['allowdesc'] == 0 && !empty($_POST['linkdesc']))
			$error = "Link Description shouldn't be filled in!";

		if ($error == NULL) {
			foreach($_POST as $key => $value)
				$$key = clean($value, 'yes');

			if (!isset($linkdesc)) $linkdesc = null;
			if (!isset($linkbutton)) $linkbutton = null;

			$editLink = $mysql->query("UPDATE `".$dbpref."links` SET
				`ownername` = '".$ownername."',
				`owneremail` = '".strtolower($email)."',
				`linkname` = '".$linkname."',
				`linkurl` = '".$linkurl."', 
				`linkbutton` = '".$linkbutton."',
				`linkdesc` = '".$linkdesc."',
				`linktags` = '".$linktags."',
				`category` = '".(int)$linkcat."',
				`approved` = '".(int)$approve."',
				`dateupdated` = '". TODAY ."'
			WHERE `id` = ".(int)$_GET['id']." LIMIT 1");
			
			if ($editLink) {
				if ($opt['emailuser'] == 1 && ($status == 0 && $approve == 1))
					doEmail($email, "Link '".html_entity_decode($linkname)."' Approved", $opt['approvalmail']);
				
				echo '<p><b class="red">Note:</b> The link was successfully edited.';
				
				if ($status == 0 && (int)$approve == 1)
					echo ' The link was also <b>approved</b> and will now appear in your directory.';

				echo ' <a href="manage_links.php">Return to Manage Links</a>.</p>';
			} else {
				echo '<p><b class="red">Note:</b> There was an error, the link could not be added. Please try again; or, contact your host for help if the MySQL connection has failed.</p>';
			}
		}
	}
	
	if (isset($error))
		echo '<p class="red">'.$error.'</p>';
	
	$getlink = $mysql->query("SELECT `".$dbpref."links`.*, `".$dbpref."categories`.`catname` FROM `".$dbpref."links` LEFT JOIN `".$dbpref."categories` ON `".$dbpref."links`.`category` = `".$dbpref."categories`.`id` WHERE `".$dbpref."links`.`id` = ".(int)$_GET['id']." LIMIT 1");
	if (mysql_num_rows($getlink) == 1) {
		$link = mysql_fetch_assoc($getlink);
?>
		<form action="manage_links.php?v=edit&amp;id=<?php echo $link['id']; ?>" method="post" id="linkform">
		<fieldset>
			<input type="hidden" name="linkid" id="linkid" value="<?php echo md5($opt['salt'] . $link['id']); ?>" />
			<input type="hidden" name="status" id="status" value="<?php echo $link['approved']; ?>" />

			<label for="ownername">Name</label>
			<input type="text" name="ownername" id="ownername" value="<?php echo $link['ownername']; ?>" />
			
			<label for="email">E-mail Address</label>
			<input type="text" name="email" id="email" value="<?php echo $link['owneremail']; ?>" />

			<label for="linkname">Link Name</label>
			<input type="text" name="linkname" id="linkname" value="<?php echo $link['linkname']; ?>" />

			<label for="linkurl">Link URL</label>
			<input type="text" name="linkurl" id="linkurl" value="<?php echo $link['linkurl']; ?>" />

			<?php if (!empty($link['linkbutton']))
				echo '<span class="label">Current button</span> <span class="button"><img src="'.$opt['dirlink'].'imgs/'.$link['linkbutton'].'" alt="" /><br /><small>(Remove file path from box below to delete button)</small></span>'; ?>
			<?php if ($opt['allowbutton'] == 1) : ?>
			<label for="linkbutton">Link Button Path</label>
			<input type="text" name="linkbutton" id="linkbutton" value="<?php echo $link['linkbutton']; ?>" />
			<?php endif; ?>

			<?php if ($opt['allowdesc'] == 1) : ?>
			<label for="linkdesc">Link Description</label>
			<textarea name="linkdesc" id="linkdesc" rows="10" cols="5"><?php echo $link['linkdesc']; ?></textarea>
			<?php endif; ?>

			<label for="linktags">Link Tags</label>
			<input type="text" name="linktags" id="linktags" value="<?php echo $link['linktags']; ?>" />

			<label for="linkcat">Link Category</label>
			<select name="linkcat" id="linkcat">
			<?php
				getAllCats('dropdown', '&nbsp;&nbsp;', $link['category']);
			?>
			</select>
	
			<?php if ($_GET['v'] == "approve") : ?>
			<label for="approve" class="red"><b>Approve?</b></label>
			<select name="approve" id="approve">
				<option value="1" selected="selected">YES</option>
				<option value="0">NO</option>
			</select>
			<?php else : ?>
			<input type="hidden" name="approve" id="approve" value="<?php echo $link['approved']; ?>" />
			<?php endif; ?>

			<input type="submit" name="submit" class="button" value="Edit Link" />
		</fieldset>
		</form>
<?php
	} else {
		echo "<p>Oh noes! There ain't no linky to be edited with that ID, matey.</p>";
	}
break;
default:
?>
	<h1>Manage Links</h1>
	
<?php
	$from = ((getPage() * $opt['perpage']) - $opt['perpage']);

	$adminLinks = $mysql->query("SELECT `".$dbpref."links`.*, DATE_FORMAT(`dateadded`, '%D %b %Y') AS `date`, `".$dbpref."categories`.`catname` FROM `".$dbpref."links` LEFT JOIN `".$dbpref."categories` ON `".$dbpref."links`.`category` = `".$dbpref."categories`.`id` ORDER BY `dateadded` DESC LIMIT ".$from.", ".$opt['perpage']);
?>
	<form action="manage_links.php?v=delete" method="post">
	<p>
		<input type="hidden" name="smashyhashy" id="smashyhashy" value="<?php echo md5($opt['salt'] . date("H")); ?>" />
	</p>
	<table>
	<tr><th>Link URL</th> <th>Link Name</th> <th>Description</th> <th>Category</th> <th>Date Added</th> <th colspan="2">Admin</th></tr>
<?php
	$rowCount = 0;
	while ($l = mysql_fetch_assoc($adminLinks)) {
		if ($rowCount % 2) $rowClass = 'linkeven';
		else $rowClass = 'linkodd';
		if ($l['approved'] == 0) $rowClass = 'pending';
		
		echo '
			<tr class="'.$rowClass.'"><td><a href="'.$l['linkurl'].'">'.$l['linkurl'].'</a></td> 
			<td>'.$l['linkname'].'</td> 
			<td>'.wordwrap($l['linkdesc'], 50, '<br />').'</td> 
			<td>'.$l['catname'].'</td> <td>'.$l['date'].'</td> 
			
			<td class="center">
		';

		if ($l['approved'] == 0)
			echo '<a href="manage_links.php?v=approve&amp;id='.$l['id'].'"><img src="../njicons/approve.gif" title="approve this" alt="approve" /></a>';

		echo '
				<a href="manage_links.php?v=edit&amp;id='.$l['id'].'"><img src="../njicons/edit.gif" title="edit this" alt="edit" /></a>
			</td>
			<td class="center"><input type="checkbox" name="del['.$l['id'].']" value="'.$l['id'].'" /></td>
		</tr>'."\r\n";
		
		++$rowCount;
	}
?>
	</table>
	<p class="right"><input type="submit" name="submit" value="Delete" /></p>
	</form>
<?php
	getPagination(getStats("total"));
break;
}
include('../footer.php');
?>