<?php
/**
 * Plugin Name.
 *
 * @package   DD_Arrets_Admin
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */
 
// bootstrap classes
 
include_once( plugin_dir_path( dirname(__FILE__ ) ) . 'bootstrap.php');

/**
 * @package DD_Arrets_Admin
 * @author  Your Name <email@example.com>
 */
class DD_Arrets_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	
	
	protected $nouveautes;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @TODO:
		 *
		 * - Rename "DD_Arrets" to the name of your initial plugin class
		 *
		 */
		$plugin = DD_Arrets::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_arrets_page' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_alertes_page' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_users_page' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_sent_page' ) );		
				
		// Settings for database, use test tables
		add_action( 'admin_init', array( $this, 'register_dd_arrets_settings' ) );
		// Day to send alertes
		add_action( 'admin_init', array( $this, 'register_day_alertes' ) );
		
		// url to list of dates
		add_action( 'admin_init', array( $this, 'register_url_list' ) );
		// url to one date
		add_action( 'admin_init', array( $this, 'register_url' ) );			
		// url to one arret
		add_action( 'admin_init', array( $this, 'register_url_arret' ) );	

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Custom classes fot plugin
		
		// Mode live or test
		$mode = get_option('dd_arrets_mode'); 
		
		$this->nouveautes = new Nouveautes($mode);	

	}
		
	public static function dd_create_plugin_tables(){
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'dd_alertes';

	    $sql = "CREATE TABLE $table_name (
	      id int(11) NOT NULL AUTO_INCREMENT,
	      alerte_id varchar(255) DEFAULT NULL,
	      send DATE,
	      user int(11),
	      UNIQUE KEY id (id)
	    );";
	
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    
	    dbDelta( $sql );
    
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @TODO:
	 *
	 * - Rename "DD_Arrets" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), DD_Arrets::VERSION );
			wp_enqueue_style( 'jquery.ui.theme', plugins_url( 'assets/css/jquery-ui.css', __FILE__ ), array(), DD_Arrets::VERSION );
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @TODO:
	 *
	 * - Rename "DD_Arrets" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), DD_Arrets::VERSION );
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-datepicker');		
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 * Administration Menus: http://codex.wordpress.org/Administration_Menus
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 */
		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'Arrêts Settings', $this->plugin_slug ),
			__( 'Arrêts Settings', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}
	
	public function add_plugin_arrets_page(){
		
		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_slug,
			__( 'Liste des arrêts', $this->plugin_slug ),
			__( 'Liste des arrêts', $this->plugin_slug ),
			'manage_options', 
			$this->plugin_slug.'-liste',
			array( $this, 'display_plugin_arrets_page' )
		);
		
	}

	public function add_plugin_alertes_page(){
		
		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_slug,
			__( 'Email alertes', $this->plugin_slug ),
			__( 'Email alertes', $this->plugin_slug ),
			'manage_options', 
			$this->plugin_slug.'-alertes',
			array( $this, 'display_plugin_alertes_page' )
		);
		
	}	

	public function add_plugin_users_page(){
		
		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_slug,
			__( 'Abos des utilisateurs', $this->plugin_slug ),
			__( 'Abos des utilisateurs', $this->plugin_slug ),
			'manage_options', 
			$this->plugin_slug.'-users',
			array( $this, 'display_plugin_users_page' )
		);
		
	}	
	
	public function add_plugin_sent_page(){
	
		$this->plugin_screen_hook_suffix = add_submenu_page(
			$this->plugin_slug,
			__( 'Alertes envoyés', $this->plugin_slug ),
			__( 'Alertes envoyés', $this->plugin_slug ),
			'manage_options', 
			$this->plugin_slug.'-sent',
			array( $this, 'display_plugin_sent_page' )
		);
	}
		
	public function register_dd_arrets_settings(){
	    register_setting( 'dd-arrets-settings-group', 'dd_arrets_mode' );	
	}
	
	public function register_day_alertes(){
	    register_setting( 'dd-arrets-day-alertes', 'dd_day_alertes' );	
	}
	
	public function register_url_list(){
		register_setting( 'dd-arrets-url-list', 'dd_arrets_url_list' );	
	}
	
	public function register_url_arret() {
	    //register our settings
	    register_setting( 'dd-newsletter-url-arret', 'dd_newsletter_url_arret' );
	}
	
	public function register_url() {
	    //register our settings
	    register_setting( 'dd-newsletter-url', 'dd_newsletter_url' );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_arrets_page() {
		include_once( 'views/list.php' );
	}
	
	/**
	 * Render the list of arrets page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}
	
	/**
	 * Render the list of alertes to send
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_alertes_page() {
		include_once( 'views/alertes.php' );
	}

	/**
	 * Render the list of users with abos
	 *
	 * @since    1.0.0
	 */	
	public function display_plugin_users_page(){
		include_once( 'views/users.php' );
	}

	/**
	 * Render the list of alertes send to users
	 *
	 * @since    1.0.0
	 */		
	public function display_plugin_sent_page(){
		include_once( 'views/sent.php' );
	}
	
	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(array('settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'),$links);

	}

}
