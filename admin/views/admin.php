<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<h3>Mode</h3>	
	
	<form method="post" action="options.php">
	
	    <?php settings_fields( 'dd-arrets-settings-group' ); ?>
	    <?php do_settings_sections( 'dd-arrets-settings-group' ); ?>
	    
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Mode (test ou non)</th>
	        <td><input type="text" name="dd_arrets_mode" value="<?php echo get_option('dd_arrets_mode'); ?>" /></td>
	        </tr>
	    </table>
	
	    <?php submit_button(); ?>
	
	</form>

</div>
