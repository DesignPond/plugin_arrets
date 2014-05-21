<?php
/**
 * Represents the view for the list of arrets.
 *
 * @package   DD_arrets
 * @author    Cindy Leschaud cindy.leschaud@gmail.com
 * @license   GPL-2.0+
 * @link      http://designpond.ch
 * @copyright 2014 DesignPond
 */
 
 $mode = get_option('dd_arrets_mode'); 
 
 $dd_nouveautes = new Nouveautes($mode);
 $dd_utils      = new Utils();
 $dd_grab       = new Grab();
 $dd_dates      = new Dates($mode);
 $dd_user       = new User($mode);
 $dd_html       = new Html($mode);
 
 $list = $dd_nouveautes->nouveautesQuery(15);
 
 $list = $dd_utils->objectToArray($list);
 
 //Create an instance of our table class...
 $testListTable = new Table($list);
 
 //Fetch, prepare, sort, and filter our data...
 $testListTable->prepare_items();
 
 $urlArret = ( get_option('dd_newsletter_url_arret') ? get_option('dd_newsletter_url_arret') : $url ); 
		
 $urlRoot  = ( get_option('dd_newsletter_url') ? get_option('dd_newsletter_url') : $root ); 
    

?>

<div class="wrap">
	

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
	<?php 
	
		// $testListTable->display(); 
		$week_list = array();
		$day_list  = array();
		// Empty arrays to be sur for the merge
		
		// What day is it
		$currentday = date("N");

		// Get date to update
		// See if it's today and not in the database already
		// Should see if everything is updated to...
		$last = $dd_grab->getLastDates($urlRoot);
		$date = $dd_dates->lastDateToInsert($last);
		
		// Get arrets for day
		$day_arrets = $dd_nouveautes->getArretsAndCategoriesForDates('2014-05-07');
		
		// Get users for day
		$day_users  = $dd_user->getUserAbos('all');
		
		if(!empty($day_arrets['arrets']))
		{
			// Assign arrets
			$day_list = $dd_nouveautes->assignArretsUsers($day_users, $day_arrets['arrets']);		
			// Clean users with id
			$day_list = $dd_nouveautes->cleanEachUser($day_list); 
							
		}
				
		// If it's friday friday! we need all week date range to
		if($currentday == 3)
		{
			// Get 5 last week days
			$week_days   = $dd_nouveautes->getWeekDays();		
			// Get arrets for week
			$week_arrets = $dd_nouveautes->getArretsAndCategoriesForDates($week_days);			
			// Get users for week
			$week_users  = $dd_user->getUserAbos('one');
				
			if(!empty($week_arrets['arrets']))
			{					
				// Assign arrets
				$week_list   = $dd_nouveautes->assignArretsUsers($week_users, $week_arrets['arrets']);
				// Clean users with id
				$week_list   = $dd_nouveautes->cleanEachUser($week_list); 
			}
		}
		
		// merge all users 		
		$everything = $day_list + $week_list;
		
		if(!empty($everything)){
			
			foreach($everything as $user => $arrets){
				
				echo $dd_html->setEmailHtml($user,$arrets);
				
			}
			
		}


	?>
	
</div>
