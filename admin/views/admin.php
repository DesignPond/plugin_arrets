<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   DD_arrets
 * @author    Cindy Leschaud cindy.leschaud@gmail.com
 * @license   GPL-2.0+
 * @link      http://designpond.ch
 * @copyright 2014 DesignPond
 */
 
 $mode = get_option('dd_arrets_mode'); 
 
 $dd_dates = new Dates($mode);
 $dd_grab  = new Grab();

?>

<div class="wrap">
	
	<?php
		/*
			Return values
			
			0 or false 
			1 or true
			2 : problem for date
			3 : nothing to update
			4 : insert problem
			5 : date failed			
		*/
		
		if(isset($_GET['update-result']))
		{
			switch ($_GET['update-result']) {
			    case TRUE:
			        echo '<div id="setting-error-settings_updated" class="updated"><p><strong>Date inséré</strong></p></div>';
			        break;
			    case FALSE:
			        echo '<div id="setting-error-settings_updated" class="error"><p><strong>Problème avec l\'insertion</strong></p></div>';
			        break;
			    case 2:
			        echo '<div id="setting-error-settings_updated" class="error"><p><strong>Problème avec la date</strong></p></div>';
			        break;
			    case 3:
			        echo '<div id="setting-error-settings_updated" class="error"><p><strong>Rien à mettre a jour</strong></p></div>';
			        break;	
			    case 4:
			        echo '<div id="setting-error-settings_updated" class="error"><p><strong>Problème avec l\'insertion</strong></p></div>';
			        break;	
			    case 5:
			        echo '<div id="setting-error-settings_updated" class="error"><p><strong>La date n\'est pas utilisable</strong></p></div>';
			        break;				    			      
			}			
		}	  		

	?>

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<h3>Mode</h3>	
	
	<form method="post" action="options.php">
	
	    <?php settings_fields( 'dd-arrets-settings-group' ); ?>
	    <?php do_settings_sections( 'dd-arrets-settings-group' ); ?>
	    
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Mode (test ou live)</th>
	        <td><input type="text" name="dd_arrets_mode" value="<?php echo get_option('dd_arrets_mode'); ?>" /></td>
	        </tr>
	    </table>
	
	    <?php submit_button(); ?>
	
	</form>
	
	<h3>Date dernière mise à jour</h3>
	<p><strong><?php echo $dd_dates->lastDayInDb(); ?></strong></p>
	
	<h3>Insérer date</h3>
	
	<form method="post" action="admin-post.php">
	
		<table style="width:400px" class="form-table">
	        <tr valign="top">
		        <th scope="row">Date</th>
				<td>
					<input type="hidden" name="action" value="insert-date" />
					<input type="text" id="datepicker" name="insert_date" value="" />
					<input type="hidden" id="inser-date-format" name="inser-date-format" />
				</td>
				<td><input type="submit" value="Insérer" class="button button-primary" id="submit" name="submit">	</td>
		    </tr>
		</table>	
	
	</form>
	
	<?php
	
		$list = $dd_grab->getLastDates('http://relevancy.bger.ch/AZA/liste/fr/');
		
		$list = array('140518');
		
		if($dd_dates->lastDateToInsert($list))
		{
			echo 'ok pour mettre la date a jour';
		}
		else
		{
			echo 'pas a mettre a jour';
		}
		
	?>	
	
</div>
