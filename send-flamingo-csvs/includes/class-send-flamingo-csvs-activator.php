<?php

/**
 * Fired during plugin activation
 *
 * @link       https://abdelrahmanma.com
 * @since      1.0.0
 *
 * @package    send_flamingo_csvs
 * @subpackage send_flamingo_csvs/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    send_flamingo_csvs
 * @subpackage send_flamingo_csvs/includes
 * @author     Abdelrahman Muhammad <contact@abdelrahmanma.com>
 */
class Send_Flamingo_Csvs_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'flamingo/flamingo.php' ) ) {

			die('Plugin NOT activated: Sorry, but this Plugin requires "Flamingo" to be installed and active.' );
		}
	}

}
