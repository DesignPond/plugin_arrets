<?php
/**
 * Represents the view for the list of alertes sent.
 *
 * @package   DD_arrets
 * @author    Cindy Leschaud cindy.leschaud@gmail.com
 * @license   GPL-2.0+
 * @link      http://designpond.ch
 * @copyright 2014 DesignPond
 */
 
 $mode = get_option('dd_arrets_mode'); 
 
 $dd_alertes  = new Alertes($mode);  

?>

<div class="wrap">
	
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		
	<?php 
		
		$alertes = $dd_alertes->getAlertesSent();
		
		$alertes = $dd_alertes->filterByDay($alertes);
		
		echo '<pre>';
		print_r($alertes);
		echo '</pre>';
			
							
		echo '<table class="form-table">';
						
		if( !empty($alertes) )
		{
		    foreach($alertes as $date => $alerteInfo)
		    {
		   		echo '<tr>';
		   		
			   		echo '<th>'.$date.'</th>';
			   		echo '<td>';
			   		echo '<table border="0" cellpadding="0"  cellspacing="0">';
			   		
				    foreach($alerteInfo as $alerte)
				    {	
				    	$user       = $alerte->user;
				    	  		
						$infos      = get_userdata($user);
						$user_id    = $infos->ID;
						$first_name = $infos->first_name;
						$last_name  = $infos->last_name;
						$date_abo   = get_user_meta($user, 'date_abo_active', true); 
						$rythme_abo = get_user_meta($user, 'rythme_abo', true); 
			
						echo '<tr>';
							echo '<td>'.$first_name.' '.$last_name.'</td>';
							echo '<td>'.$alerte->alerte_id.'</td>';
							echo '<td>'.$alerte->user_email.'</td>';
						echo '</tr>';	
					}
					
					echo '</table>';
					echo '</td>';
					
				echo '</tr>';		   
		    }
		}	
			
		echo '</table>';

	?>
	
</div>
