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
	
		$testListTable->display(); 

	?>
	
</div>
