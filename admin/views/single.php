<?php

 // require wordpress bootstrap
 require_once( $_SERVER["DOCUMENT_ROOT"].'/wp-load.php' );
	
 $mode = get_option('dd_arrets_mode'); 
 
 $dd_nouveautes = new Nouveautes($mode);
 $dd_utils      = new Utils();
 $dd_grab       = new Grab();
 $dd_dates      = new Dates($mode);
 $dd_user       = new User($mode);
 $dd_html       = new Html($mode);
 
 $dd_sendalert  = new Sendalert();
 
 $current   = get_option('dd_day_alertes'); 	    	
 $activeday = ($current ? $current : 5);
 // Empty arrays to be sur for the merge

 // What day is it
 $currentday = date("N");

 $root    = 'http://relevancy.bger.ch/AZA/liste/fr/';		
 $urlRoot = ( get_option('dd_arrets_url_list') ? get_option('dd_arrets_url_list') : $root ); 
		
 // Get date to update
 // See if it's today and not in the database already
 // Should see if everything is updated to...
 $last = $dd_grab->getLastDates($urlRoot);
 $date = $dd_dates->lastDateToSend($last);

 // Post variables		
 $currentday = (isset($_POST['day_number']) ? $_POST['day_number'] : $currentday ); 
 $date       = (isset($_POST['test_date'])  ? $_POST['test_date']  : $date ); 
    
 // all users 		
 $everything = $dd_sendalert->prepareAlert($date,$currentday);
 
 
 if(!empty($everything)){
					
	foreach($everything as $user => $arrets)
	{				
		$html = $dd_html->setAlerteHtml($user,$arrets);	
		
		echo $html;
		
		$us    = get_userdata( $user );
		$email = $us->user_email;
		
		if($user == 4)
		{
			add_filter( 'wp_mail_content_type', create_function('', 'return "text/html"; '));
			wp_mail('cindy.leschaud@gmail.com', 'Nouveaux arrêts depuis Droit pour le praticien', $html);
			wp_mail('cindy.leschaud@unine.ch', 'Nouveaux arrêts depuis Droit pour le praticien', $html);
			wp_mail('pruntrut@yahoo.fr', 'Nouveaux arrêts depuis Droit pour le praticien', $html);	
		}						
					
			
	}			
 }