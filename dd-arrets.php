<?php
/**
 * The WordPress Plugin DD_Arrets.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   DD_Arrets
 * @author    Cindy Leschaud <cindy.leschaud@gmail.com>
 * @license   GPL-2.0+
 * @link      http://designpond.ch
 * @copyright 2014 DesignPond
 *
 * @wordpress-plugin
 * Plugin Name:       DD_Arrets
 * Plugin URI:        http://designpond.ch
 * Description:       Mise a jour du TF et Envoi d'alertes emails quotidienne/hebdomadaires depuis www.droitpraticien.ch 
 * Version:           1.0.0
 * Author:            Cindy Leschaud
 * Author URI:        http://designpond.ch
 * Text Domain:       dd_arrets-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * Require plugin main class
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-dd-arrets.php' );

// bootstrap classes
require_once( plugin_dir_path( __FILE__ ) . 'bootstrap.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'DD_Arrets', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'DD_Arrets', 'deactivate' ) );


/*----------------------------------------------------------------------------*
 * Schedules for cron jobs
 *----------------------------------------------------------------------------*/

/*===== Cron for ARRETS UPDATES ======*/

// register when plugin is activated or deactivated
register_activation_hook( __FILE__  , 'add_arrets_schedule' );
register_deactivation_hook( __FILE__, 'clear_arrets_schedule');

// Hook crons
function add_arrets_schedule()
{
	// Schedule an action if it's not already scheduled
	if ( ! wp_next_scheduled( 'dd_daily_arrets' ) ) 
	{
		wp_schedule_event( time(), 'daily', 'dd_daily_arrets' );
	}
}

function clear_arrets_schedule()
{
	wp_clear_scheduled_hook('dd_daily_arrets');
}

/*===== Cron for ALERTES ======*/

// register when plugin is activated or deactivated
register_activation_hook( __FILE__  , 'add_alerte_schedule' );
register_deactivation_hook( __FILE__, 'clear_alerte_schedule');

// Main function to register cron jobs
function add_alerte_schedule()
{
	// Schedule an action if it's not already scheduled
	if ( ! wp_next_scheduled( 'dd_daily_alert' ) ) 
	{
	    // wp_schedule_event( time() + 300 , 'daily', 'dd_daily_alert' ); // Add 5 minutes to wait for update to finish
	}
}

function clear_alerte_schedule()
{
	// wp_clear_scheduled_hook('dd_daily_alert');
}

/**
 * Plugin is loaded
 */
add_action( 'plugins_loaded', array( 'DD_Arrets', 'get_instance' ) );

/**
 * Custom shortcodes for plugin
*/
// add_shortcode('unsuscribe_newsletter', array( 'DD_Arrets', 'unsuscribe_newsletter_shortcode' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-name-admin.php` with the name of the plugin's admin file
 * - replace DD_Arrets_Admin with the name of the class defined in
 *   `class-plugin-name-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-dd-arrets-admin.php' );
	add_action( 'plugins_loaded', array( 'DD_Arrets_Admin', 'get_instance' ) );
	
	register_activation_hook( __FILE__, array( 'DD_Arrets_Admin', 'dd_create_plugin_tables' ) );
}
