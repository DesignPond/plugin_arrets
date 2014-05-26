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
 
 $dd_utils    = new Utils();
 $dd_user     = new User($mode); 
 $dd_database = new Database($mode);  

?>

<div class="wrap">
	
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		
	<?php 
		
		$all = array();
		$one = array();
		
		$categories = $dd_database->getCategories();
		
		$all = $dd_user->getUserAbos('all');
		$one = $dd_user->getUserAbos('one');
		
		$users = $all + $one;
		
		if(!empty($users)){
							
			foreach($users as $user => $abos)
			{
				$infos      = get_userdata($user);
				$user_id    = $infos->ID;
				$first_name = $infos->first_name;
				$last_name  = $infos->last_name;
				$date_abo   = get_user_meta($user, 'date_abo_active', true); 
				$rythme_abo = get_user_meta($user, 'rythme_abo', true); 
								
				echo '<table border="0" class="form-table dd_users" cellpadding="0"  cellspacing="0">';
					
					echo '<tr class="dd_table_user_header">
						  <th><strong>'.$user_id.'</strong> | '.$first_name.' '.$last_name.'</th>
						  <th>Date d\'abo: <strong>'.$date_abo.'</strong></th>
						  <th>Rythme d\'abo: <strong>'.$rythme_abo.'</strong></th>
					</tr>';
					
					echo '<tr class="dd_table_header"><th>Categorie</th><th>Mots clés</th><th>Que publications?</th></tr>';
					
					foreach($abos as $cat => $abo){
						echo '<tr>';							
							echo '<th>'.$cat.' | '.$categories[$cat][0].'</th>';
							echo '<td>';
							
								if(!empty($abo['keywords']))
								{
									foreach($abo['keywords'] as $words){
										echo $words.'<br/>';
									}
								}
								
							echo '</td>';
							echo '<td>';							
								echo (!empty($abo['ispub']) ? 'Oui': '');								
							echo '</td>';							
						echo '</tr>';
					}
					
				echo '</table>';
			}						
		}		
		
	?>
	
</div>
