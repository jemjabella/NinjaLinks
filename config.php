<?php
//-----------------------------------------------------------------------------
// NinjaLinks Copyright © Jem Turner 2007-2009 unless otherwise noted
// http://www.jemjabella.co.uk/
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License. See README.txt
// or LICENSE.txt for more information.
//-----------------------------------------------------------------------------


// DATABASE SETTINGS
$dbhost = "localhost";   // this can usually be left as localhost
$dbuser = "ninjalinks";   // database username
$dbpass = "temp";   // database user password
$dbname = "ninjalinks";   // the name of the database
$dbpref = "nl_";   // the prefix for the tables (e.g. dir_)

// SCRIPT SETTINGS
$opt['user'] = "admin"; // admin username
$opt['pass'] = "password"; // admin password
$opt['email'] = "you@your-website.com"; // admin email address
$opt['salt'] = "mmsalty"; // like a second password. you won't have to remember it, so make it long & random!

$opt['dirname'] = "NinjaLinks Directory"; // the name of your directory
$opt['dirlink'] = "http://www.your-website.com/ninjalinks/"; // the url your directory will be installed in - end with a slash: /
$opt['uploaddir'] = "/home/username/public_html/ninjalinks/"; // the absolute path to a folder you wish to upload buttons too (only necessary if allowbutton = 1)

$opt['emailnew'] = 1; // (1 = yes, 0 = no)  email admin on new link submission
$opt['emailuser'] = 1; // (1 = yes, 0 = no)  email user when link is approved

$opt['approvalmail'] = "Thank you for submitting a link to ".$opt['dirname']." - your link has now been approved.";

$opt['topdirlinks'] = 1; // (1 = yes, 0 = no) allow links submitted in top level categories
$opt['opentarget'] = 0; // (1 = yes, 0 = no) target to new windows?
$opt['nofollow'] = 0; // (1 = yes, 0 = no) add nofollow attribute to links, see http://www.jemjabella.co.uk/blog/google-pagerank-and-nofollow for nofollow info
$opt['perpage'] = 20; // amount of links per page

$opt['allowdesc'] = 1; // (1 = yes, 0 = no) allow site descriptions
$opt['allowtags'] = 0; // (1 = yes, 0 = no) allow tags (in future, will enable search by tag, tag clouds etc)
$opt['allowbutton'] = 1; // (1 = yes, 0 = no) allow link buttons
$opt['allowdupes'] = 0; // (1 = yes, 0 = no) allow duplicate links

$opt['buttonmaxwidth'] = 88; // max width allowed for buttons
$opt['buttonmaxheight'] = 31; // max height allowed for buttons
$opt['buttonsize'] = 15360; // max button size in bytes, default is 15KB (15360)

$opt['maxkarma'] = 4; // max karma before link is rejected (recommend 4)
$opt['required'] = array("email", "linkname", "linkurl", "linkcat"); // required fields - use field name/id from addlink.php

$opt['cleanupdates'] = 1; // (1 = yes, 0 = no) remove HTML from updates in control panel; disable at your own risk

// DO NOT REMOVE THIS ....
require_once('functions.php');
?>