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
	if (!isset($_POST['lovelyjubbly']) || $_POST['lovelyjubbly'] != md5($opt['salt'] . date("H")))
		exit('<p>Invalid token. <a href="manage_categories.php">Try again</a>?</p>');
	
	doDelete("categories", $_POST['del']);
break;
case "add":
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$error = NULL;

		if (!is_numeric($_POST['catparent']))
			$error = "Invalid Parent Category, please fix and try again.";
		elseif (empty($_POST['catname']))
			$error = "Must enter a Category Name";
		elseif (preg_match("/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/i", $_POST['catname']))
			$error = "Category Name contains invalid characters, please fix and try again.";
		elseif ($mysql->single("SELECT `id` FROM `".$dbpref."categories` WHERE `catname` = '".clean($_POST['catname'], 'yes')."' LIMIT 1"))
			$error = "A category with that name already exists.";

		if ($error == NULL) {
			foreach($_POST as $key => $value)
				$$key = clean($value, 'yes');
			
			$addCat = $mysql->query("INSERT INTO `".$dbpref."categories` (`catname`, `catparent`) VALUES ('".$catname."', '".(int)$catparent."')");
			
			if ($addCat) {
				echo '<p><b class="red">Note:</b> The category was successfully added. <a href="manage_categories.php">Return to Manage Categories</a>.</p>';
			} else {
				echo '<p><b class="red">Note:</b> There was an error, the category could not be added. Please try again; or, contact your host for help if the MySQL connection has failed.</p>';
			}
		}
	}
	
	if (isset($error))
		echo '<p class="red">'.$error.'</p>';
	
?>
	<form action="manage_categories.php?v=add" method="post" id="linkform">
	<fieldset>
		<label for="catname">Category Name</label>
		<input type="text" name="catname" id="catname" />
		
		<label for="catparent">Parent Category</label>
		<select name="catparent" id="catparent">
			<option value="0">None</option>
		<?php
			getAllCats('dropdown', '&nbsp;&nbsp;', null, 1);
		?>
		</select>

		<input type="submit" name="submit" class="button" value="Add Cat." />
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
		if ($_POST['catid'] != md5($opt['salt'] . $_GET['id']))
			exit('<p>Category IDs do not match</p>');
		
		if (!is_numeric($_POST['catparent']))
			$error = "Invalid Parent Category, please fix and try again.";
		elseif (!ereg("^[A-Za-z0-9]*$", $_POST['catname']))
			$error = "Category Name contains invalid characters, please fix and try again.";
		
		if ($error == NULL) {
			foreach($_POST as $key => $value)
				$$key = clean($value, 'yes');

			$editCat = $mysql->query("UPDATE `".$dbpref."categories` SET
				`catname` = '".$catname."',
				`catparent` = '".(int)$catparent."'
			WHERE `id` = ".(int)$_GET['id']." LIMIT 1");
			
			if ($editCat) {
				echo '<p><b class="red">Note:</b> The category was successfully edited.';
				
				echo ' <a href="manage_categories.php">Return to Manage Categories</a>.</p>';
			} else {
				echo '<p><b class="red">Note:</b> There was an error, the category could not be edited. Please try again; or, contact your host for help if the MySQL connection has failed.</p>';
			}
		}
	}
	
	if (isset($error))
		echo '<p class="red">'.$error.'</p>';
	
	$getcat = $mysql->query("SELECT * FROM `".$dbpref."categories` WHERE `id` = ".(int)$_GET['id']." LIMIT 1");
	if (mysql_num_rows($getcat) == 1) {
		$cat = mysql_fetch_assoc($getcat);
?>
		<form action="manage_categories.php?v=edit&amp;id=<?php echo $cat['id']; ?>" method="post" id="linkform">
		<fieldset>
			<input type="hidden" name="catid" id="catid" value="<?php echo md5($opt['salt'] . $cat['id']); ?>" />

			<label for="catname">Category Name</label>
			<input type="text" name="catname" id="catname" value="<?php echo $cat['catname']; ?>" />
			
			<label for="catparent">Parent Category</label>
			<select name="catparent" id="catparent">
				<option value="0">None</option>
			<?php
				getAllCats('dropdown', '&nbsp;&nbsp;', $cat['catparent'], 1);
			?>
			</select>

			<input type="submit" name="submit" class="button" value="Edit Cat." />
		</fieldset>
		</form>
<?php
	} else {
		echo "<p>Oh noes! There ain't no category to be edited with that ID, matey.</p>";
	}
break;
default:
?>
	<h1>Manage Categories</h1>
	<p><a href="?v=add">Add a Category</a></p>

<?php
	$from = ((getPage() * $opt['perpage']) - $opt['perpage']);

	$adminCats = $mysql->query("SELECT `id`, `catname`, `catparent` AS `temp`, (SELECT `catname` FROM `".$dbpref."categories` WHERE `id` = `temp`) AS `subcatname` FROM `".$dbpref."categories` ORDER BY `catname` ASC LIMIT ".$from.", ".$opt['perpage']);
?>
	<form action="manage_categories.php?v=delete" method="post">
	<p>
		<input type="hidden" name="lovelyjubbly" id="lovelyjubbly" value="<?php echo md5($opt['salt'] . date("H")); ?>" />
	</p>
	<table>
	<tr><th>Category</th> <th>Subcategory Of..</th> <th colspan="2">Admin</th></tr>
<?php
	$rowCount = 0;
	while ($c = mysql_fetch_assoc($adminCats)) {
		if ($rowCount % 2) $rowClass = 'linkeven';
		else $rowClass = 'linkodd';
	
		echo '
			<tr class="'.$rowClass.'"><td>'.$c['catname'].'</td> 
			<td>'.$c['subcatname'].'</td> 
			<td class="center">
		';

		echo '
				<a href="manage_categories.php?v=edit&amp;id='.$c['id'].'"><img src="../njicons/edit.gif" title="edit this" alt="edit" /></a>
			</td>
			<td class="center"><input type="checkbox" name="del['.$c['id'].']" value="'.$c['id'].'" /></td>
		</tr>'."\r\n";
		
		++$rowCount;
	}
?>
	</table>
	<p class="right"><input type="submit" name="submit" value="Delete" /></p>
	</form>
<?php
	getPagination($mysql->single("SELECT COUNT(`id`) FROM `".$dbpref."categories`"));
break;
}
include('../footer.php');
?>