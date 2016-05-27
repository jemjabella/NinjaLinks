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


<h1><?php echo $opt['dirname']; ?></h1>
<p>There are <?php echo getStats("total"); ?> links in the database; <?php echo getStats("approved"); ?> approved and <?php echo getStats("pending"); ?> pending.</p>


<h2>Latest Added Links</h2>
<?php getLinks(0, 2, 'all'); ?>


<h2>Updates</h2>
<?php getUpdates(5); ?>


<?php
include('footer.php');
?>