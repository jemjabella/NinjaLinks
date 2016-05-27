<?php
//-----------------------------------------------------------------------------
// NinjaLinks Copyright © Jem Turner 2007-2009 unless otherwise noted
// http://www.jemjabella.co.uk/
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License. See README.txt
// or LICENSE.txt for more information.
//-----------------------------------------------------------------------------


// IMPORTANT FUNCTIONS -- DO NOT EDIT
class mysql {
	function mysql($host, $user, $pass, $dbnm) {
		$connect = mysql_connect($host, $user, $pass) or doError('no-connect');
		$select = mysql_select_db($dbnm, $connect) or doError('no-select-db');
		
		$this->query("SET NAMES 'utf8'");
	}

	function query($query) {
		$result = mysql_query($query);
		if (!$result)
			doError('query-fail');

		return $result;
	}
	function single($query) {
		$result = $this->query($query);
		if (!$result)
			exit('Could not run query: ' . mysql_error());
		else
			return @mysql_result($result, 0, 0);
	}
}
$mysql = new mysql($dbhost, $dbuser, $dbpass, $dbname);


define('TODAY', gmdate("Y-m-d H:i:s"));


// DATA MANIPULATION AND VALIDATION FUNCTIONS
function clean($input, $fordb = 'yes') {
	$input = str_replace("<3", "&lt;3", $input);
	$input = htmlentities(strip_tags(urldecode($input)), ENT_NOQUOTES, 'UTF-8');
	
	if ($fordb == "yes")
		$input = escape($input);
	
	return trim($input);
}
function escape($input) {
	if (get_magic_quotes_gpc())
		$input = stripslashes($input);

	return mysql_real_escape_string($input);
}

function nl2p($pee, $br = 1) {
	/* THANK YOU MATT - http://ma.tt & http://wordpress.org */
	$pee = $pee . "\n"; // just to make things a little easier, pad the end
	$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
	// Space things out a little
	$allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|style|script|object|input|param|p|h[1-6])';
	$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
	$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
	$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
	$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
	$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
	$pee = preg_replace( '|<p>(<div[^>]*>\s*)|', "$1<p>", $pee );
	$pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
	$pee = preg_replace( '|<p>|', "$1<p>", $pee );
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
	$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
	$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
	$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
	$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
	if ($br) {
		$pee = preg_replace('/<(script|style).*?<\/\\1>/se', 'str_replace("\n", "<PreserveNewline />", "\\0")', $pee);
		$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
		$pee = str_replace('<PreserveNewline />', "\n", $pee);
	}
	$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
	$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
	if ( strstr( $pee, '<pre' ) )
		$pee = preg_replace('!(<pre.*?>)(.*?)</pre>!ise', " stripslashes('$1') .  stripslashes(clean_pre('$2'))  . '</pre>' ", $pee);
	$pee = preg_replace( "|\n</p>$|", '</p>', $pee );
/**/
	return $pee;
}
function make_excerpt($entry, $excerpt_length, $extension, $cutword = 'false') {
	$entry = strip_tags($entry);
	$cutmarker = "**cut_here**";
	if (strlen($entry) > $excerpt_length) {
		$entry = wordwrap($entry, $excerpt_length, $cutmarker, $cutword);
		$entry = explode($cutmarker, $entry);
		$entry = $entry[0] . $extension;
	}
	return $entry;
}
function checkBots() {
	$isbot = false;

	$bots = array("Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot", "[en]", "0.6 Beta", "build", "OpenWare", "Opera/9.0 (Windows NT 5.1; U; en)");
	foreach ($bots as $bot)
		if (strpos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
			$isbot = true;

	if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ")
		$isbot = true;
	
	return $isbot;
}
function spamCount($input) {
	$spam = array("beastial", "bestial", "blowjob", "clit", "cum", "cunilingus", "cunillingus", "cunnilingus", "cunt", "ejaculate", "fag", "felatio", "fellatio", "fuck", "fuk", "fuks", "gangbang", "gangbanged", "gangbangs", "hotsex", "jism", "jiz", "orgasim", "orgasims", "orgasm", "orgasms", "phonesex", "phuk", "phuq", "porn", "pussies", "pussy", "spunk", "xxx", "viagra", "phentermine", "tramadol", "adipex", "advai", "alprazolam", "ambien", "ambian", "amoxicillin", "antivert", "blackjack", "backgammon", "texas", "holdem", "poker", "carisoprodol", "ciara", "ciprofloxacin", "debt", "dating", "porn");

	$words = array();
	foreach (preg_split('/[^\w]/', strtolower($input), -1, PREG_SPLIT_NO_EMPTY) as $word)
		$words[] = $word;
	
	$compare = array_intersect($spam, $words);

	return count($compare);
}
function exploitKarma($input) {
	$tempKarma = (int)0;
	
	$exploits = array("content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript");
	foreach ($exploits as $exploit)
		if (!empty($input) && stripos($input, $exploit) !== false)
			$tempKarma += 2;
	
	return $tempKarma;
}
function badMailKarma($input) {
	$tempKarma = (int)0;
	
	$badmails = array("mail.ru", "hotsheet.com", "ibizza.com", "aeekart.com", "fooder.com", "yahone.com");
	$domain = array_pop(explode("@", $input));
	foreach($badmails as $ext)
		if ($domain == $ext)
			$tempKarma += 2;
	
	return $tempKarma;
}
function isBanned($email) {
	global $mysql, $opt, $dbpref;
	
	$list = array();
	$getbanned = $mysql->query("SELECT * FROM `".$dbpref."banned`");
	if (mysql_num_rows($getbanned)) {
		while ($r = mysql_fetch_assoc($getbanned))
			if ($r['type'] == "ip")
				$list['ip'][] = $r['value'];
			else
				$list['email'][] = strtolower($r['value']);
		
		if (in_array($_SERVER['REMOTE_ADDR'], $list['ip']))
			return true;
		elseif (in_array($email, $list['email']))
			return true;
		else
			return false;
	} else {
		return false;
	}
}
function validateButton($button) {
	global $opt;

	$allowed = array(".jpg", ".gif", ".png");
	$errors = array();

	if (filesize($button) > $opt['buttonsize'])
		$error[] = "Button larger than max file size";
	elseif (in_array(ext($button), $allowed))
		$error[] = "Invalid file type";
	
	if (!$imginfo = @getimagesize($button)) {
		$error[] = "Invalid file - images only.";
	} else {
		if ($imginfo[0] > $opt['buttonmaxwidth'])
			$error[] = "Button too wide; max width: ".$opt['buttonmaxwidth'];
		elseif ($imginfo[1] > $opt['buttonmaxheight'])
			$error[] = "Button too high; max height: ".$opt['buttonmaxheight'];
		elseif ($imginfo[2] == 4)
			$error[] = "Only jpg, gif and png buttons are supported.";
	}
	
	if (!isset($error) || count($error) > 0)
		return $button;
	else
		return $error;
}

// for bug testing purposes
function niceArray($input) {
	echo '<pre>';
	print_r($input);
	echo '</pre>';
}

// GET FUNCTIONS
function ext($file) {
	return strrchr($file, ".");
}
function getButton($button) {
	global $opt;

	if (function_exists('curl_init')) {
		// curl stuff from: http://www.slowerbetter.com/2006/09/19/alternative-to-doing-file-operations-on-a-url-using-curl-in-php/
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $button);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$timeout = 5;
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		$buttContents = curl_exec($ch);
		curl_close($ch);

		// pre-emptive null byte stripping before we rename - pointless?
		$button = str_replace("\0", "", $button);
		
		$temp = $opt['uploaddir'] . time() . "-" . rand() . ext($button);

		if (!file_exists($temp)) {
			$fp = fopen($temp, "w+");

			if (fwrite($fp, $buttContents) === FALSE) {
				fclose($fp);
				unlink($fp);
				return null;
			}
			fclose($fp);
		}
		
		$results = validateButton($temp);
		
		if ($results == $temp)
			return $temp;
		else
			return $results;
	} else {
		return null;
	}
}
function getAllCats($display = 'dropdown', $spacer = '&nbsp;&nbsp;', $selected = null, $level = 2) {
	global $mysql, $opt, $dbpref;
	
	/* 
		this is probably the single most hackiest piece of SHIT I've ever written... nonetheless
		it fulfills it purpose and will enable me to get the script out some time this milennium
		if you're knowledgeable enough to be reading this far down - forgive me, please!
	*/

	$cats = array();
	$meow = $mysql->query("SELECT *, `".$dbpref."categories`.`id` as `catid`, COUNT(`".$dbpref."links`.`id`) AS `linkcount` FROM `".$dbpref."categories` LEFT JOIN `".$dbpref."links` ON `".$dbpref."categories`.`id` = `".$dbpref."links`.`category` GROUP BY `".$dbpref."categories`.`id` ORDER BY `catparent`, `catname`");
	while($row = mysql_fetch_assoc($meow)) {
		if ($row['catparent'] == 0)
			$cats[$row['catid']] = array('name' => $row['catname'], 'subcats' => "", 'linkcount' => $row['linkcount']);
		else
			$cats[$row['catparent']]['subcats'][$row['catid']] = array('name' => $row['catname']);
	}
	
	if ($opt['topdirlinks'] == 0) $disallow = ' disabled="disabled"';
	else $disallow = NULL;
	
	foreach($cats as $catid => $catinfo) {
		foreach($catinfo as $key => $value) {
			if ($key == "name") {
				if ($display == "dropdown") {
					if ($selected != null && $selected == $catid) $showsel = ' selected="selected"';
					else $showsel = null;
					
					echo '<option value="'.$catid.'"'.$disallow.$showsel.'>'.$value.'</option>'."\r\n";
				} else {
					if ($opt['topdirlinks'] == 1)
						echo '<li><a href="links.php?cat='.$catid.'">'.$value.'</a> ('.$cats[$catid]['linkcount'].' links)</li>'."\r\n";
					else
						echo '<li>'.$value.'</li>'."\r\n";
				}
			} elseif ($key == "subcats") {
				if (is_array($value))
					foreach($value as $subcatid => $subcat) {
						if ($level == 2) {
							foreach ($subcat as $subkey => $info)
								if ($subkey == "name") {
									if ($display == "dropdown") {
										if ($selected != null && $selected == $subcatid) $showsel = ' selected="selected"';
										else $showsel = null;
										
										echo '<option value="'.$subcatid.'"'.$showsel.'>'.$spacer.$info.'</option>'."\r\n";
									} else {
										echo '<li><a href="links.php?cat='.$subcatid.'">'.$spacer.$info.'</a></li>'."\r\n";
									}
								}
						}
					}
			}
		}
	}
}
function getLinks($offset, $limit, $category) {
	global $mysql, $opt, $dbpref;
	
	$buildQuery = "SELECT `".$dbpref."links`.*, `".$dbpref."categories`.`catname` FROM `".$dbpref."links` LEFT JOIN `".$dbpref."categories` ON `".$dbpref."links`.`category` = `".$dbpref."categories`.`id` WHERE `".$dbpref."links`.`approved` = 1";
	if ($category != "all") $buildQuery .= " AND `category` = ".$category;
	$buildQuery .= " ORDER BY `dateadded` DESC";

	$links = $mysql->query($buildQuery." LIMIT ".$offset.", ".$limit);
	echo '<ul>';
	while ($l = mysql_fetch_assoc($links)) {
		echo '<li><a href="click.php?link='.$l['id'].'"';
		if ($opt['opentarget'] == 1) echo ' target="_blank"';
		if ($opt['nofollow'] == 1) echo ' rel="nofollow"';
		echo '>';
			if ($opt['allowbutton'] == 1 && !empty($l['linkbutton']))
				echo '<img src="'.$opt['dirlink'].'imgs/'.$l['linkbutton'].'" alt="'.$l['linkname'].'" />';
			else
				echo $l['linkname'];
		echo '</a> <em>('.$l['hits'].' hits)</em>';
		if ($opt['allowdesc'] == 1)
			echo '<br />'.$l['linkdesc'].'</li>';
		else
			echo '</li>';
	}
	echo '</ul>';
}
function getUpdates($limit) {
	global $mysql, $opt, $dbpref;
	
	$updates = $mysql->query("SELECT *, DATE_FORMAT(`datetime`, '%a %D %b %Y \- %H:%i') AS `date` FROM `".$dbpref."updates` ORDER BY `datetime` DESC LIMIT ".$limit);
	while ($u = mysql_fetch_assoc($updates)) {
?>
		<h2><?php echo $u['title']; ?></h3>
		<?php echo nl2p($u['entry']); ?>
		<p class="updatemeta">Posted on <?php echo $u['date']; ?></p>
<?php
	}
}
function getStats($stat) {
	global $mysql, $dbpref;
	
	if ($stat == "total")
		return $mysql->single("SELECT COUNT(`id`) FROM `".$dbpref."links`");
	elseif ($stat == "approved")
		return $mysql->single("SELECT COUNT(`id`) FROM `".$dbpref."links` WHERE `approved` = 1");
	elseif ($stat == "pending")
		return $mysql->single("SELECT COUNT(`id`) FROM `".$dbpref."links` WHERE `approved` = 0");
}
function getPage() {
	if (!isset($_GET['pg']) || empty($_GET['pg']) || !is_numeric($_GET['pg']))
		$page = 1;
	else
		$page = (int)$_GET['pg'];
	
	return $page;
}
function getView() {
	if (isset($_GET['v']) && ereg("^[A-Za-z0-9]*$", $_GET['v']))
		$view = $_GET['v'];
	else
		$view = null;
	
	return $view;
}
function getPagination($total) {
	global $opt;

	$totalPages = ceil($total / $opt['perpage']);
	
	echo '<p class="center">Pages: ';
	for ($x = 1; $x <= $totalPages; $x++) {
		if ($x == getPage()) echo '<strong class="current">'.$x.'</strong> ';
		else echo '<a href="'.basename($_SERVER['PHP_SELF']).'?pg='.$x.'">'.$x.'</a> ';
	}
	echo '</p>';
}

// DO FUNCTIONS
function doEmail($recipient, $subject, $message, $xtraheaders = '') {
	global $opt;

	if (strstr($_SERVER['SERVER_SOFTWARE'], "Win"))
		$headers = "From: ".$opt['email'];
	else 
		$headers = "From: ".$opt['dirname']." <".$opt['email'].">";
	
	$headers .= $xtraheaders;

	if (mail($recipient, $subject, $message, $headers))
		return true;
	else
		return false;
}
function doCheckLogin() {
	global $opt;

	if (!isset($_SESSION['nlLogin'])) {
		return false;
	} else {
		if ($_SESSION['nlLogin'] == md5($opt['user'].md5($opt['pass'].$opt['salt'])))
			return true;
		else
			return false;
	}
}
function doDelete($table, $ids) {
	global $mysql, $dbpref;

	if (isset($ids) && is_array($ids)) {
		foreach($ids as $id)
			if (!is_numeric($id)) exit("<p>Invalid update id selected.</p>");
		
		$delete = $mysql->query("DELETE FROM `".$dbpref.$table."` WHERE `id` IN (". implode(", ", $ids).")");
		
		if ($delete) {
			echo '<p>Item(s) successfully deleted.</p>';
		} else {
			echo '<p>Item(s) not deleted; please check for errors and try again.</p>';
		}
	} else {
		exit("<p>Invalid (or no) item selected.</p>");
	}
}

function isInstalled() {
	global $mysql, $dbname, $dbpref;
	
	$findTables = $mysql->query("SHOW TABLES FROM `".$dbname."`");
	if (mysql_num_rows($findTables))
		return true;
	else
		return false;
}
function checkInstall() {
	if (!isInstalled())
		doError('not-installed');
	elseif (file_exists("install.php"))
		doError('install-file');
}

function doError($errorID) {
	// at some point I shall set this up to do something with $details - store or w/e
	
	switch($errorID) {
	case "no-connect":
		$displaymsg = 'Could not connect to the database. Please check your database details and try again.';
	break;
	case "no-select-db":
		$displaymsg = 'Could not select the database. Please check your database details and try again.';
	break;
	case "not-installed":
		$displaymsg = 'NinjaLinks is not installed; please run install.php';
	break;
	case "install-file":
		$displaymsg = 'You have not deleted the install.php file; please do so to continue.';
	break;
	case "query-fail":
		$displaymsg = 'Could not run query on the database. Please check your database details and try again.';
	break;
	default:
		$displaymsg = 'Unidentified error.';
	break;
	}

	echo '<p style="background: #fff; color: #f00; font-weight: bold;">Error: '.$displaymsg.'</p>';
	include('footer.php');
	exit;
}

error_reporting(0);
?>