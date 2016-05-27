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
?>

<h1>Add Your Link</h1>

<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$error = NULL;
	$karma = (int)0;

	if (checkBots() === true)
		doError("No bots allowed.");
	
	foreach($_POST as $key => $value) {
		if (in_array($key, $opt['required']) && empty($value))
			$error = $key.' is a required field.';
	
		$karma += spamCount($value) * 2;
	}
	
	if (isset($_POST['linkdesc']) && !empty($_POST['linkdesc']))
		$karma += exploitKarma($_POST['linkdesc']);
	
	if (isset($_POST['email']) && !empty($_POST['email']))
		$karma += badMailKarma($_POST['email']);
			
	if (!empty($_POST['linkdesc']) && preg_match("/(<.*>)/i", $_POST['linkdesc']))
		$karma += 2;
	if (!empty($_POST['ownername']) && strlen($_POST['ownername']) < 3 || strlen($_POST['ownername']) > 15)
		$karma += 2;
	if (strlen($_POST['linkurl']) > 30)
		$karma += 2;
	if (!empty($_POST['linkdesc']) && substr_count($_POST['linkdesc'], 'http') >= 1)
		$karma += 2;
	if (!empty($_POST['linkdesc']) && substr_count($_POST['linkdesc'], 'http') >= 3)
		$karma += 4;

	$_POST['email'] = strtolower($_POST['email']);
	
	if (preg_match("/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/i", $_POST['ownername']))
		$error = "Name contains invalid characters. Please fix and try again.";
	elseif (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$", $_POST['email']))
		$error = "Invalid E-mail Address, please fix and try again.";
	elseif (!preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $_POST['linkurl']))
		$error = "Invalid Link URL, please fix and try again. Link must start with 'http://' and contain no special characters.";
	elseif (!is_numeric($_POST['linkcat']))
		$error = "Invalid Category, please fix and try again. Do not tamper with the form.";
	elseif (isBanned($_POST['email']) === true)
		$error = "There was an error whilst trying to add your link to the directory.";

	if ($opt['allowbutton'] == 0 && !empty($_POST['linkbutton']))
		$error = "Button URL shouldn't be filled in! Do not tamper with the form.";
	elseif ($opt['allowbutton'] == 1 && !empty($_POST['linkbutton']))
		if (!preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $_POST['linkbutton']))
			$error = "Invalid Button URL, please fix and try again. Button URL must start with 'http://' and contain no special characters.";
	
	if ($opt['allowdesc'] == 0 && !empty($_POST['linkdesc']))
		$error = "Link Description shouldn't be filled in! Do not tamper with the form.";
		
	if ($opt['allowdupes'] == 0 && !empty($_POST['linkurl'])) {
		$findLink = $mysql->query("SELECT * FROM `".$dbpref."links` WHERE `linkurl` LIKE '%".clean($_POST['linkurl'], 'yes')."%' LIMIT 1");
		if (mysql_num_rows($findLink) == 1)
			$error = "Duplicate link detected - please only add your website once.";
	}
	
	if ($karma > $opt['maxkarma'])
		$error = "Your link seems awfully spammy, and has been rejected.";

	if ($error == NULL) {
		foreach($_POST as $key => $value) 
			$$key = clean($value, 'yes');
			
		if (isset($linkbutton) && !empty($linkbutton)) {
			$butOutput = getButton($linkbutton);
			if (is_array($butOutput)) exit(print_r($error));
			else $linkbutton = basename($butOutput);
		} else {
			$linkbutton = null;
		}
		if (!isset($linkdesc)) $linkdesc = null;
		if (!isset($linktags)) $linktags = null;
		
		$addLink = $mysql->query("INSERT INTO `".$dbpref."links` (`ownername`, `owneremail`, `linkname`, `linkurl`, `linkbutton`, `linkdesc`, `linktags`, `category`, `approved`, `dateadded`) VALUES ('".$ownername."', '".$email."', '".$linkname."', '".$linkurl."', '".$linkbutton."', '".$linkdesc."', '".$linktags."', '".(int)$linkcat."', 0, '". TODAY ."')");
		if ($addLink) {
			if ($opt['emailnew'] == 1) {
				$message = "New link pending approval in ".html_entity_decode($opt['dirname'])."\r\n\r\n";
				
				$message .= "Owner: ".html_entity_decode($ownername)." (".$email.")\r\n";
				$message .= "Site Name: ".html_entity_decode($linkname)."\r\n";
				$message .= "Site URL: ".$linkurl."\r\n";
				$message .= "Link Tags: ".html_entity_decode($linktags)."\r\n";
				$message .= "Description: ".html_entity_decode($linkdesc)."\r\n\r\n";
				
				$message .= "You must approve this link before it will appear in your directory.\r\n";
				$message .= $opt['dirlink']."admin/manage_links.php\r\n\r\n";
				
				$message .= "Submission info:\r\n";
				$message .= "Date: ". TODAY ."\r\n";
				$message .= "Karma: ". $karma ."\r\n";
				$message .= "User IP: ".$_SERVER['REMOTE_ADDR']."\r\n";
				$message .= "Browser: ".$_SERVER['HTTP_USER_AGENT'];
			
				doEmail($opt['email'], "Link added to ".$opt['dirname'], $message);
			}
			
			echo '<p>Thank you for submitting a link to my directory. Your link should be added shortly if it passes moderation.</p>';
		} else {
			echo '<p>There was an error, the link could not be added. Please contact the website admin for more information.</p>';
		}
	}
}

if (isset($error))
	echo '<p class="red">'.$error.'</p>';
?>


<form action="addlink.php" method="post" id="linkform">
	<fieldset>
		<label for="ownername">Your Name</label>
		<input type="text" name="ownername" id="ownername" />
		
		<label for="email">E-mail Address</label>
		<input type="text" name="email" id="email" />

		<label for="linkname">Link Name</label>
		<input type="text" name="linkname" id="linkname" />

		<label for="linkurl">Link URL</label>
		<input type="text" name="linkurl" id="linkurl" />

		<?php if (isset($opt['allowbutton']) && $opt['allowbutton'] == 1) : ?>
		<label for="linkbutton">Link Button URL</label>
		<input type="text" name="linkbutton" id="linkbutton" />
		<?php endif; ?>

		<?php if (isset($opt['allowdesc']) && $opt['allowdesc'] == 1) : ?>
		<label for="linkdesc">Link Description</label>
		<textarea name="linkdesc" id="linkdesc" rows="10" cols="5"></textarea>
		<?php endif; ?>

		<?php if (isset($opt['allowtags']) && $opt['allowtags'] == 1) : ?>
		<label for="linktags">Link Tags</label>
		<input type="text" name="linktags" id="linktags" />
		<?php endif; ?>

		<label for="linkcat">Link Category</label>
		<select name="linkcat" id="linkcat">
		<?php
			getAllCats();
		?>
		</select>

		<input type="submit" name="submit" class="button" value="Add Link" />
	</fieldset>
</form>



<?php
include('footer.php');
?>