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

$error_msg = NULL;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$karma = (int)0;
	
	if (checkBots() === true)
		doError("No bots allowed.");
		
	foreach($_POST as $key => $value) {
		$karma += spamCount($value) * 2;
	}
	
	$karma += exploitKarma($_POST['comments']);
	$karma += badMailKarma($_POST['email']);
	
	if (!empty($_POST['comments']) && preg_match("/(<.*>)/i", $_POST['comments']))
		$karma += 2;
	if (!empty($_POST['name']) && strlen($_POST['name']) < 3 || strlen($_POST['name']) > 15)
		$karma += 2;
	if (strlen($_POST['url']) > 30)
		$karma += 2;
	if (!empty($_POST['comments']) && substr_count($_POST['comments'], 'http') >= 1)
		$karma += 2;
	if (!empty($_POST['comments']) && substr_count($_POST['comments'], 'http') >= 3)
		$karma += 4;
		
	$_POST['email'] = strtolower($_POST['email']);
	
	if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['comments']))
		$error_msg .= "Name, e-mail and comments are required fields. \n";
	elseif (strlen($_POST['name']) > 15)
		$error_msg .= "The name field is limited at 15 characters. Your first name or nickname will do! \n";
	elseif (!ereg("^[A-Za-z' -]*$", $_POST['name']))
		$error_msg .= "The name field must not contain special characters. \n";
	elseif (!ereg("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$", strtolower($_POST['email'])))
		$error_msg .= "That is not a valid e-mail address. \n";
	elseif (!empty($_POST['url']) && !preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $_POST['url']))
		$error_msg .= "Invalid Link URL, please fix and try again.\n";

	if ($karma > $opt['maxkarma'])
		$error_msg = "Your message seems awfully spammy, and has been rejected. \n";
		
	if ($error_msg == NULL) {
		foreach ($_POST as $key => $val)
			$$key = clean($val, 'no');
		
		$message = "You received this e-mail message through your directory: \r\n\r\n";
		
		$message .= "Name: ".$name."\r\n";
		$message .= "E-mail: ".$email."\r\n";
		$message .= "Website: ".$url."\r\n";
		$message .= "Comments: ".$comments."\r\n\r\n";
		
		$message .= "Message Info\r\n";
		$message .= "Date: ". TODAY ."\r\n";
		$message .= "IP: ".$_SERVER['REMOTE_ADDR']."\r\n";
		$message .= "Browser: ".$_SERVER['HTTP_USER_AGENT'];

		if (doEmail($opt['email'], "Mail From ".$opt['dirname'], $message, "\r\nReply-To: ".$email)) {
			echo "<p>Your mail was successfully sent.</p>";
		} else {
			echo "<p>Your mail could not be sent this time.</p>";
		}
	}
}

if ($error_msg != NULL) {
	echo "<p><strong style='color: red;'>ERROR:</strong><br />";
	echo nl2br($error_msg) . "</p>";
}
?>
<h1>Contact Directory Owner</h1>

<form action="contact.php" method="post" id="linkform">
<fieldset>
	<label for="name">Name</label>
	<input type="text" name="name" id="name" value="" />
	
	<label for="email">E-mail</label>
	<input type="text" name="email" id="email" value="" />
	
	<label for="url">Website</label>
	<input type="text" name="url" id="url" value="" />
	
	<label for="comments">Comments</label>
	<textarea name="comments" id="comments"></textarea>
	
	<input type="submit" name="submit" id="submit" class="button" value="Send" />
</fieldset></form>

<br class="clearer" />

<?php
include('footer.php');
?>