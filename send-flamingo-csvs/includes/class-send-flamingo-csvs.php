<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://abdelrahmanma.com/
 * @since      1.0.0
 *
 * @package    send_flamingo_csvs
 * @subpackage send_flamingo_csvs/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    send_flamingo_csvs
 * @subpackage send_flamingo_csvs/includes
 * @author     Abdelrahman Muhammad <contact@abdelrahmanma.com>
 */
class Send_Flamingo_Csvs {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Send_Flamingo_Csvs_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $send_flamingo_csvs    The string used to uniquely identify this plugin.
	 */
	protected $send_flamingo_csvs;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'Send_Flamingo_Csvs_VERSION' ) ) {
			$this->version = Send_Flamingo_Csvs_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->send_flamingo_csvs = 'send-flamingo-csvs';

		$this->load_dependencies();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - send_flamingo_csvs_Loader. Orchestrates the hooks of the plugin.
	 * - Send_Flamingo_Csvs_Admin. Defines all hooks for the admin area.
	 * - send_flamingo_csvs_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-send-flamingo-csvs-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-send-flamingo-csvs-admin.php';

        /**
         * Helper Functions.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/functions/autoloader.php';

		/**
         * The Custom Post Types.
         */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/cpt/sfc_cronjob.php';

		/**
         * The Custom Meta-boxes for Custom Post Types.
         */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/sfc-cronjob.php';

		$this->loader = new Send_Flamingo_Csvs_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Send_Flamingo_Csvs_Admin( $this->get_send_flamingo_csvs(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_sfc_dashboard' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_one_time_sfc_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_sfc_settings' );
		add_action('send_monthly_sfc_cronjob', 'send_sfc_mail');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_send_flamingo_csvs() {
		return $this->send_flamingo_csvs;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Send_Flamingo_Csvs_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
