<?php  
/*
Plugin Name: Custom Sender for Email Before Download 
Plugin URI: http://blog.pc-magus.hu/custom-sender-for-email-before-download/
Version: 0.1
Author: Andras Guseo 
Author URI: http://blog.pc-magus.hu
Description: The plugin lets you set your custom sender for the Email Before Download (version 3.3) plugin. 
Text-domain: pcs-csebd
Licence: GPL2
*/


	/** Step 2 (from text above). */
	add_action( 'admin_menu', 'csebd_plugin_menu' );
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'pcm_csebd_action_links' );
	load_plugin_textdomain('pcm-csebd', false, basename( dirname( __FILE__ ) ) . '/languages' );

	/** Step 1. We add the option page */
	function csebd_plugin_menu() {
		// We check if the Email Before Download plugin is activated or not ...
		if ( !is_plugin_active( 'email-before-download/email-before-download.php' ) ) {
			// ... if not, then we deactivate this plugin and give an error message.
			deactivate_plugins( plugin_basename( __FILE__ ) );
			$args = array('back_link' => true);
			wp_die( __( 'Custom Sender: You need the Email Before Download plugin in order for this plugin to work.', 'pcm-csebd' ), __( 'Plugin missing', 'pcm-csebd' ), $args );
		}
		// If activated, then we create the admin page.
		add_options_page( __('Custom Sender for Email Before Download Options', 'pcm-csebd'), __( 'Custom Sender for EBD', 'pcm-csebd'), 'manage_options', 'pcm-csebd', 'csebd_options' );
		
	}
	
	/** Step 3. Options page*/
	function csebd_options() {
		// Check for rights
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'pcm-csebd' ) );
		}

		
		if ( $_POST['action'] == 'update' ) {
			//Form data sent, then clean name and email and format to "name <email@domain.com>"
			$name = trim ( preg_replace ( '/[^0-9a-zA-ZáàäéèíóöőüúűÁÉÍÓÖŐÚÜŰ_\s\-]/', '', $_POST[sendername] ) );
			$value = substr( $name , 0, 25) . " <" . sanitize_email($_POST[senderemail]) . ">";
			// Update value in table
			update_option('email_before_download_email_from', $value);
		} 
		else {
			// Normal page display
			// Get the value from the db
			$value = get_option('email_before_download_email_from');
		}
		// Split the value to name and email address
		$sendername = trim ( substr ( $value, 0, strpos( $value, "<" ) ) );
		$senderemail = str_replace( array( "<", ">" ), "", strstr( $value, "<" ) );
	?>

	<?php // The page ?>
	<div class="wrap pcm-csebd">
		<style>
			.pcm-csebd label { display: inline-block; width: 100px; }
			.pcm-csebd .helptext { font-style: italic; margin-bottom: 20px; margin-left: 110px; }
			.pcm-csebd .sample { margin-top: 20px; font-weight: bold; display: inline-block; }
		</style>
		<h2><?php _e( 'Custom Sender for Email Before Download', 'pcm-csebd' ); ?></h2>
		<p><?php _e( 'Here you can specify the sender for the <a href="https://wordpress.org/plugins/email-before-download/" target="_blank">Email Before Download</a> Plugin.', 'pcm-csebd' ); ?></p>
		<form method="post">
			<?php
				settings_fields( 'pcm_csebd_settings_group' );
				do_settings_sections( 'pcm_csebd_settings_group' );
			?>
			<div class="c-wrap">
				<label for="sender"><?php _e( 'Name:', 'pcm-csebd' ); ?></label>
				<input type="text" id='sendername' class="pcm_csebd" value="<?php echo $sendername; ?>" name="sendername" />
			</div>
			<div class="helptext"><?php printf( __( 'Max. length: %d chars.', 'pcm-csebd'), '25'); ?><br/><?php _e( 'Accepted characters:', 'pcm-csebd'); ?> a-z, A-Z, 0-9, _, -, á, à, ä, é, è, í, ó, ö, ő, ú, ü, ű, Á, É, Í, Ó, Ö, Ő, Ú, Ü, Ű</div>
			<div class="c-wrap">
				<label for="sender"><?php _e( 'Email address:', 'pcm-csebd'); ?></label>
				<input type="text" id='senderemail' class="pcm_csebd" value="<?php echo $senderemail; ?>" name="senderemail" />
			</div>
			<div class="c-wrap">
				<label><?php _e( 'Sample:', 'pcm-csebd'); ?></label>
				<span class="sample"><?php echo $sendername . " &lt;" . $senderemail . "&gt;"; ?></span>
			</div>
			<?php submit_button(); ?>
		</form>
	</div>
	
	<?php
	}
	
	function pcm_csebd_action_links( $links ) {
		$links[] = '<a href="'. get_admin_url(null, 'options-general.php?page=pcm-csebd') .'">'. __( 'Settings', 'pcm-csebd' ) .'</a>';
		//$links[] = '<a href="http://my.domain.com" target="_blank">More plugins by Me</a>';
		return $links;
	}
?>