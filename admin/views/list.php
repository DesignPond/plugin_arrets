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
 
 $dd_user       = new User($mode);
 
 $list = $dd_nouveautes->nouveautesQuery(15);
 
 $list = $dd_utils->objectToArray($list);
 
 //Create an instance of our table class...
 $testListTable = new Table($list);
 
 //Fetch, prepare, sort, and filter our data...
 $testListTable->prepare_items();
    

?>

<div class="wrap">
	

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
	<?php 
	
		// $testListTable->display(); 
		
		// What day is it
		$currentday = date("N");
		
		// If it's friday friday! we need all week date range to
		if($currentday == 3)
		{
			// Get days
			$week_days   = $dd_nouveautes->getWeekDays();
			
			// Get arrets for week
			$week_arrets = $dd_nouveautes->getArretsAndCategoriesForDates($week_days);
			
			// Get users for week
			$week_users  = $dd_user->getUserAbos('all');
			
			// Assign arrets
			$list = $dd_nouveautes->assignArretsUsers($week_users, $week_arrets);
			
			// Clean users with id
			//$list = $dd_nouveautes->cleanEachUser($list); 
			
			echo '<pre>';
			print_r($list);
			echo '</pre>';
		}

		
		// Users
		// Arrets
					
		//$list = $dd_nouveautes->assignArretsUsers($users, $arrets);
		//$list = $dd_nouveautes->cleanEachUser($list); 
					
		
	?>
	
</div>
