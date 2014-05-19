<?php 

include_once('arret_autoloader.php');
// Register the directory to your include files
Arret_AutoLoader::registerDirectory('admin/classes');

require_once( dirname(dirname(dirname(dirname(__FILE__)))).'/wp-load.php' );
require_once( dirname(dirname(dirname(dirname(__FILE__)))).'/wp-admin/includes/admin.php' );
