<?php 

if (function_exists('plugin_dir_path')) 
{
    $path =  plugin_dir_path( __FILE__ );
} 
else 
{
    $path = dirname(dirname(dirname(dirname(__FILE__)))).'/wp-content/plugins/dd_arrets/';
    
    require_once( dirname(dirname(dirname(dirname(__FILE__)))).'/wp-load.php' );
}

include_once('arret_autoloader.php');
// Register the directory to your include files
Arret_AutoLoader::registerDirectory( $path . 'admin/classes');