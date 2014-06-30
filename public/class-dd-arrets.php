<?php
/**
 * Plugin Name.
 *
 * @package   DD_Arrets
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

/**
 * @package DD_Arrets
 * @author  Cindy Leschaud cindy.leschaud@gmail.com
 */
class DD_Arrets {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * @TODO - Rename "plugin-name" to the name of your plugin
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'dd-arrets';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	/**
	 * Custom classes
	*/
	
	protected $dates;
	
	protected $grab;
	
	protected $insert;
	
	protected $update;
	
	protected $nouveautes;
	
	protected $user;
	
	protected $html;

	protected $sendalert;
	
	protected $log;
	
	protected $urlList;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		// Cron job		
		add_action( 'dd_daily_alert', array( $this, 'send_alertes' ) );
		add_action( 'dd_daily_arrets', array( $this, 'update_arrets' ) );
		
		// post from admin function
		add_action( 'admin_post_insert-date', array( $this, 'insert_date' ) );

		// Custom classes fot plugin
		
		$root = 'http://relevancy.bger.ch/AZA/liste/fr/';
		
		$this->urlList  = ( get_option('dd_arrets_url_list') ? get_option('dd_arrets_url_list') : $root ); 
		
		// Mode live or test
		$mode = get_option('dd_arrets_mode'); 
		
		$this->dates      = new Dates($mode);
		
		$this->grab       = new Grab();
		
		$this->insert     = new Insert($mode);	

		$this->update     = new Update($mode);	

		$this->nouveautes = new Nouveautes($mode);	

		$this->user       = new User($mode);

		$this->html       = new Html($mode);	
		
		$this->sendalert  = new Sendalert();	
						
		$this->log        = new Log();

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogsWHERE archived = '0' AND spam = '0' AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * Insert choosen date arrets from TF
	*/
	public function insert_date(){
		
		/*
			Return values
			
			0 or false 
			1 or true
			2 : problem for date
			3 : nothing to update
			4 : insert problem
			5 : date failed			
		*/
		
		$page  = admin_url( 'options-general.php?page=dd-arrets' ); // redirect url
		
		if( isset($_POST['inser-date-format']) && !empty($_POST['inser-date-format']) )
		{	
			if( $this->insert->insertForDate($_POST['inser-date-format']) )
			{
				$result = $this->update->initUpdate();
				
				$url    = add_query_arg( array('update-result' => $result) , $page );
				
				wp_redirect( $url ); 			    
				exit; 
			}
			else
			{							
				$url = add_query_arg( array('update-result' => 4) , $page );
				
				wp_redirect( $url ); 			    
				exit; 
			}
		}	
		
		$url = add_query_arg( array('update-result' => 5) , $page );
		
		wp_redirect( $url ); 			    
		exit; 
 	
	}
	
	/**
	 * Cron job fire , update arrets for day
	 * Test the day
	 */		
	public function update_arrets(){
		
		if( $this->insert->insertMissingDates() )
		{
			$this->update->initUpdate();
		}
		
		// Should see if everything is updated ... 
		if( $this->sendalert->updateOk() )
		{
			wp_mail('cindy.leschaud@gmail.com', 'Alertes', 'Arrêts mis en ligne '.time() );
		}
		else
		{
			wp_mail('cindy.leschaud@gmail.com', 'Alertes', 'Problème avec la mise à jour des arrêts');
		}
		
	}

	/**
	 * Cron job fire , sending all alertes
	 * Test the day
	 *
	 */	
	public function send_alertes(){

		// What day is it
		$currentday = date("N");

		// Get date to update
		// See if it's today and not in the database already
		$last = $this->grab->getLastDates($this->urlList);
		$date = $this->dates->lastDateToSend($last);
		
		// Should see if everything is updated ... 
		if( !$this->sendalert->updateOk() )
		{
			wp_mail('cindy.leschaud@gmail.com', 'Alertes', 'Arrêts pas updated!');
			exit();
		}
		else
		{
			// And if it's not sent already
			if( $this->sendalert->areWeSending() )
			{
				wp_mail('cindy.leschaud@gmail.com', 'Alertes', 'Alertes up up and away!');
				exit();
				//$abos = $this->sendalert->prepareAlert($date,$currentday);		
				//$this->goSendAlertes($abos);
			}
			else
			{
				wp_mail('cindy.leschaud@gmail.com', 'Alertes', 'Alertes already sent');
				exit();
			}
		}

	}
	
	/*
	 * Everything ok! Send alertes
	*/
	public function goSendAlertes($abos){

		if(!empty($abos))
		{			
			foreach($abos as $user => $arrets)
			{	
				// Get body of alerte email			
				$body_html = $this->html->setAlerteHtml($user,$arrets);	
				// Send with elasticemail and get result
				$result    = $this->sendalert->prepareSend($user , $body_html);
				// Se if we have an id for send
				$alerte_id = $this->sendalert->testIdSend($result);
		
				if($alerte_id)
				{
					// Alerte is send!  Update database with infos
					$this->updateNewsletterIsSend($alerte_id,$user);
				}
			}			
		}	
		
		wp_mail('cindy.leschaud@gmail.com', 'Résultat', 'Fin envoi alertes '.time() );
	
	}
			
	/*
	 * Insert log alert send to email 
	*/
	public function updateNewsletterIsSend($alerte_id,$user){
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'dd_alertes';

	    $wpdb->insert($table_name, array('alerte_id' => $alerte_id,  'send' => date('Y-m-d') , 'user' => $user ));
		
	}

}
