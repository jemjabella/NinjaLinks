//--------------------
// READ ME
//--------------------

NinjaLinks v1.1 Copyright © Jem Turner 2007-2009 unless otherwise noted

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.



Support is given at my leisure. If you need help, first check:
http://codegrrl.com/forums/index.php?showforum=39
..to make sure your problem isn't covered there. If it isn't, please
create a support post including the problems you're having, any errors
thrown up by the script and the version of the script you're running
If you know which version # of PHP & MySQL you're using, please include those




//--------------------
// INSTRUCTIONS
//--------------------
1. Create a database using your online control panel; see:
http://www.tutorialtastic.co.uk/tutorial/creating_a_mysql_database
2. Customise config.php - set your database preferences, username, password, etc
3. Upload all files and directories to your website in a folder of your choice
4. Run install.php and follow the instructions as necessary
5. When prompted, delete install.php - you can now use the script.

If you plan to allow users to upload buttons, you must CHMOD the buttons directory
('imgs' by default) to 777

NOTE: some hosts run PHP as CGI, which usually eradicates the need to change
the permissions on files and folders. Try adding a link with a button before
changing any permissions - if you get an error, CHMOD 777 on imgs, otherwise 
leave it as standard.


__________________________ HOW DO I CHMOD/CHANGE FOLDER PERMISSIONS?

There are lots of tutorials on CHMODing which can be found through Google:
http://www.google.com/search?q=chmod+tutorial


__________________________ HOW DO I FIT THE SCRIPT INTO MY EXISTING LAYOUT?

The script is set up to use the popular header/footer include system. That
means you add the 'top' of your layout - things like divs, header images
etc. to the header.php file and the bottom of your layout - closing notices
and copyright signs - to the footer.php file.

For more information on PHP includes (for layout purposes) see this tutorial:
http://www.tutorialtastic.co.uk/page/php_includes


__________________________ HOW DO I CONVERT FROM SIMPLEDIR?

Conversion from SimpleDir MUST be done on a fresh install!

Install NinjaLinks as per the instructions above. Open convert_from_simpledir.php
and customise the simpledir mysql connection details. When the correct details have
been inserted, upload the file to your NinjaLinks directory and access the file
(e.g. www.your-site.com/ninjalinks/convert_from_simpledir.php)

Once the process is complete, and as long as you see no errors, install.php and
convert_from_simpledir.php MUST BE DELETED.




//--------------------
// FEATURES
//--------------------
* complete category management
* link management including pending links
* allows optional link buttons with submitted links
* allows optional link description
* tree like system allows viewing by category
* optional notification emails for link owners
* pagination of links in admin panel and public directory
* secure button uploads
* spam protection and data validation to prevent malicious input
* contact form
* optional nofollow attribute for links
* hits out counter
* update form for users to manage own links


//--------------------
// COMING SOON
//--------------------
* control options via admin panel
* premium links option for paid positions
* search directory form
* sort by options in admin panel
* email link owner via admin panel
* individual pages for updates with RSS feed
* affiliate management
* link rating system
* view by tags, view by rating


//--------------------
// CHANGELOG
//--------------------
PRIORITY KEY: 
	'M' minor changes - aesthetic or non-essential fix
	'N' normal changes - required bug fix
	'I' important changes - security fix

New features/fixes in version 1.1
* (M) Fix typo in config.php
* (M) Allow spaces and dashes in category names - manage_categories.php
* (M) Fix order of updates displayed - functions.php
* (M) Add ownername field - addlink.php / manage_links.php / updatelink.php
* (N) Remove strtolower from URLs (affecting UC links) in addlink.php / manage_links.php / updatelink.php
* (N) Turn off errors - functions.php (not necessary once live)
* (N) Undeclared variable error in manage_categories.php
* (N) Allow foreign chars in link names / owner names etc - addlink.php / functions.php / install.php / updatelink.php
* (N) Smoother install process - functions.php / install.php / header.php
* (N) Better install checking (if install file exists etc) - functions.php / header.php
* (N) Better error reporting - functions.php
* (N) Improved spam protection - config.php / functions.php / addlink.php / contact.php
* (N) Option to disallow duplicate links - config.php / addlink.php


//--------------------
// CREDITS
//--------------------
Mucho thanks go to the following people for helping with NinjaLinks:

Six 	- http://soul-kiss.org/ (Nagging where necessary, providing simpledir info)
Ang 	- http://glitter.silencia.net/ (For her patience :))
Amelie	- http://not-noticeably.net (Testing)
Louise	- http://un-ordinary.net (bug testing)
Rebecca	- http://void-flower.net (version 1.1 testing)