<?php

class Alertes{

	// DB tables
	protected $alertes;
	
	protected $user_meta;
	
	protected $abo_user;
	
	protected $abo_pub_table;
	
	// Include classes
	
	protected $utils;
	
	protected $log;

	function __construct( $test = null ) {
				
		// Set tables			
		$this->alertes       = 'wp_dd_alertes';

		$this->user_meta     = 'wp_usermeta';

		$this->abo_user      = ( $test ? 'wp_user_abo_test' : 'wp_user_abo' );

		$this->abo_pub_table = ( $test ? 'wp_user_abo_pub_test' : 'wp_user_abo_pub' );
		
		// Set classes		
		$this->utils = new Utils;
		
		$this->log   = new Log;

	}
	
	public function getAlertesSent(){

		global $wpdb;
			
		$listUserAlertes = $wpdb->get_results(' SELECT    wp_users.user_email , '.$this->alertes.'.*
			 									FROM      '.$this->alertes.'
			 									LEFT JOIN '.$this->user_meta.' on '.$this->user_meta.'.user_id = '.$this->alertes.'.user 
												LEFT JOIN wp_users on wp_users.ID = '.$this->alertes.'.user 
												GROUP BY '.$this->alertes.'.id');	
		
		return $listUserAlertes;

	}
	
	public function filterByDay($alertes){
		
		$alertesByDate = array();
		
		if( !empty($alertes) )
		{
		   foreach($alertes as $alerte)
		   {
		  	  $alertesByDate[$alerte->send][] = $alerte;
		   }
		}
		
		return $alertesByDate;
	}
}