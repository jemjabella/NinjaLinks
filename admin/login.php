<?php
//-----------------------------------------------------------------------------
// NinjaLinks Copyright © Jem Turner 2007, 2008 unless otherwise noted
// http://www.jemjabella.co.uk/
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License. See README.txt
// or LICENSE.txt for more information.
//-----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	require('../config.php');
	if ($_POST['username'] == $opt['user'] && $_POST['password'] == $opt['pass']) {
		// password is correct
		session_start();

		$_SESSION['nlLogin'] = md5($opt['user'].md5($opt['pass'].$opt['salt']));
		header("Location: index.php");
		exit;
	} else {
		exit("<p>Invalid username and/or password.</p>");
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
    <title>NinjaLinks Login Form</title>
</head>
<body>
    <form method="post" action="login.php">

    Username:<br>
    <input type="text" name="username" id="username"><br>
    Password:<br>
    <input type="password" name="password" id="password"><br>

    <input type="submit" name="submit" value="Login">

    </form>
</body>
</html>