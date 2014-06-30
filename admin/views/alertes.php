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
    

?>

<div class="wrap">
	

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
		<h3>Tester dates</h3>
		
		<div class="dd_bloc_float dd_bloc_small">
			<form method="post" class="dd_alertes_form" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">		
				<table border="0" class="form-table dd_table_alertes" cellpadding="0"  cellspacing="0">
			        <tr>
			        	<td><i>Actuel</i></td>
			        	<td><i>Date: <?php echo $date;?> | Jour : <?php echo $currentday; ?></i></td>
				    </tr>	
			        <tr>
				        <td colspan="2"><strong>Changer la date et/ou le jour</strong></td>
				    </tr>
			        <tr>
				        <td>Date</td>
						<td><input type="text" id="datepicker" name="test_date" value="" /></td>
				    </tr>	
			        <tr>
				        <td>Numéro jour</td>
						<td><input style="width:50px;" type="text" name="day_number" value="" /></td>
				    </tr>			    
					<tr><td colspan="2"><input type="submit" value="Envoyer" class="button button-primary" id="submit" name="submit"></td></tr>					
				</table> 		
			</form>
		</div>
		
		<?php 
		
			// all users 		
			$everything = $dd_sendalert->prepareAlert($date,$currentday);
			
			echo '<div class="dd_bloc_float dd_bloc_big">';
			
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
							//wp_mail('cindy.leschaud@gmail.com', 'Nouveaux arrêts depuis Droit pour le praticien', $html);
							//wp_mail('cindy.leschaud@unine.ch', 'Nouveaux arrêts depuis Droit pour le praticien', $html);
							//wp_mail('pruntrut@yahoo.fr', 'Nouveaux arrêts depuis Droit pour le praticien', $html);	
						}				
							
					}			
				}
			
			echo '</div>';

		?>
	
	<hr class="dd_clear" />
	
</div>
