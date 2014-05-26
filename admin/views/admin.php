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
	        <th scope="row">Mode (test ou null/rien)</th>
	        <td class="dd_width_big"><input type="text" name="dd_arrets_mode" value="<?php echo get_option('dd_arrets_mode'); ?>" /></td>
		    <td><input type="submit" value="Enregistrer" class="button button-primary" id="submit" name="submit"></td>
	        </tr>
	    </table>

	</form>
	<br/>


	<h3>Jour d'envoi</h3>	
	
	<form method="post" action="options.php">
	
	    <?php settings_fields( 'dd-arrets-day-alertes' ); ?>
	    <?php do_settings_sections( 'dd-arrets-day-alertes' ); ?>
	    
	    <?php 
	    
	    	$current    = get_option('dd_day_alertes'); 
	    	
	    	$currentday = ($current ? $current : 5);
	    	
	    	$days = array( 1 => 'Lundi' , 2 => 'Mardi' , 3 => 'Mercredi' , 4 => 'Jeudi' , 5 => 'Vendredi');
	    ?>
	    
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Jour </th>
	        <td class="dd_width_big">
	        	<select name="dd_day_alertes">
		        	<?php
		        		
		        		foreach($days as $id => $day)
		        		{
		        			$select = ($id == $currentday ? ' selected ': '');
			        		echo '<option '.$select.' value="'.$id.'">'.$day.'</option>';
		        		}
		        	
		        	?>
	        	</select>
	        </td>
		    <td><input type="submit" value="Enregistrer" class="button button-primary" id="submit" name="submit"></td>
	        </tr>
	    </table>

	</form>
	<br/>
			
	<h3>Url principale du TF</h3>	
	
	<form method="post" action="options.php">
	
	    <?php settings_fields( 'dd-newsletter-url' ); ?>
	    <?php do_settings_sections( 'dd-newsletter-url' ); ?>
	    
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Url</th>
	        <td class="dd_width_big"><input type="text" name="dd_newsletter_url" value="<?php echo get_option('dd_newsletter_url'); ?>" /></td>
	        <td><input type="submit" value="Enregistrer" class="button button-primary" id="submit" name="submit"></td>
	        </tr>
	    </table>
	
	</form>
	<br/>
	
	<h3>Url liste des dates de publication</h3>	
	
	<form method="post" action="options.php">
	
	    <?php settings_fields( 'dd-arrets-url-list' ); ?>
	    <?php do_settings_sections( 'dd-arrets-url-list' ); ?>
	    
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Url</th>
	        <td class="dd_width_big"><input type="text" name="dd_arrets_url_list" value="<?php echo get_option('dd_arrets_url_list'); ?>" /></td>
	        <td><input type="submit" value="Enregistrer" class="button button-primary" id="submit" name="submit"></td>
	        </tr>
	    </table>

	</form>
	<br/>
			
	<h3>Url des arrêt</h3>	
	
	<form method="post" action="options.php">
	
	    <?php settings_fields( 'dd-newsletter-url-arret' ); ?>
	    <?php do_settings_sections( 'dd-newsletter-url-arret' ); ?>
	    
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Url</th>
	        <td class="dd_width_big"><input type="text" name="dd_newsletter_url_arret" value="<?php echo get_option('dd_newsletter_url_arret'); ?>" /></td>
	        <td><input type="submit" value="Enregistrer" class="button button-primary" id="submit" name="submit"></td>
	        </tr>
	    </table>
	
	</form>
		
	<h3>Date dernière mise à jour</h3>
	<p><strong><?php echo $dd_dates->lastDayInDb(); ?></strong></p>
	<br/>
	
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
				<td><input type="submit" value="Insérer" class="button button-primary" id="submit" name="submit"></td>
		    </tr>
		</table>	
	
	</form>
	
</div>
