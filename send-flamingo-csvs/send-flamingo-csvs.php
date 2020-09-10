<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://abdelrahmanma.com/
 * @since             1.0.0
 * @package           send_flamingo_csvs
 *
 * @wordpress-plugin
 * Plugin Name:       Send Flamingo CSVs
 * Plugin URI:        https://abdelrahmanma.com/send-flamingo-csvs-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Abdelrahman Muhammad
 * Author URI:        https://abdelrahmanma.com/
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       send-flamingo-csvs
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'send_flamingo_csvs_VERSION', '1.0.0' );
define( 'send_flamingo_csvs_dir', plugin_dir_path( __FILE__ ));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-send-flamingo-csvs-activator.php
 */
function activate_send_flamingo_csvs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-send-flamingo-csvs-activator.php';
	Send_Flamingo_Csvs_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-send-flamingo-csvs-deactivator.php
 */
function deactivate_send_flamingo_csvs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-send-flamingo-csvs-deactivator.php';
	Send_Flamingo_Csvs_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_send_flamingo_csvs' );
register_deactivation_hook( __FILE__, 'deactivate_send_flamingo_csvs' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-send-flamingo-csvs.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_send_flamingo_csvs() {

	$plugin = new Send_Flamingo_Csvs();
	$plugin->run();
	return $plugin;
}
$sfc_plugin = run_send_flamingo_csvs();
