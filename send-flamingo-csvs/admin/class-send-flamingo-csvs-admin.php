<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://abdelrahmanma.com
 * @since      1.0.0
 *
 * @package    send_flamingo_csvs
 * @subpackage send_flamingo_csvs/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    send_flamingo_csvs
 * @subpackage send_flamingo_csvs/admin
 * @author     Abdelrahman Muhammad <contact@abdelrahmanma.com>
 */
class Send_Flamingo_Csvs_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $send_flamingo_csvs    The ID of this plugin.
	 */
	private $send_flamingo_csvs;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $send_flamingo_csvs       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $send_flamingo_csvs, $version ) {

		$this->send_flamingo_csvs = $send_flamingo_csvs;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in send_flamingo_csvs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The send_flamingo_csvs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $post_type;
		$page = '';
		if( array_key_exists( 'page', $_GET ) ){
			$page = $_GET['page'];
		}
		if( $post_type === 'sfc_cronjob' or $page == 'send-flamingo-csvs-one-time' ){
			wp_enqueue_style( $this->send_flamingo_csvs, plugin_dir_url( __FILE__ ) . 'css/send-flamingo-csvs-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in send_flamingo_csvs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The send_flamingo_csvs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $post_type;
		$page = '';
		if( array_key_exists( 'page', $_GET ) ){
			$page = $_GET['page'];
		}
		if( $post_type === 'sfc_cronjob' or $page == 'send-flamingo-csvs-one-time' ){
			wp_enqueue_script( $this->send_flamingo_csvs, plugin_dir_url( __FILE__ ) . 'js/send-flamingo-csvs-admin.js', array( 'jquery' ), $this->version, false );
		}

	}

	public function register_sfc_dashboard()
    {
        add_menu_page('Monthly CSVs', 'Monthly CSVs', 'manage_options', 'send-flamingo-csvs-dashboard', '', 'dashicons-admin-page', 28);
	}

	public function register_one_time_sfc_page()
    {
        add_submenu_page('send-flamingo-csvs-dashboard', 'One Time Email With CSV', 'One Time', 'manage_options', 'send-flamingo-csvs-one-time', array($this, 'sfc_onetime_page'));
	}

	public function sfc_onetime_page()
	{
		require_once('partials/send-flamingo-csvs-admin-one-time.php');
	}

	public function register_sfc_settings()
    {
        register_setting( 'sfc_settings', 'sfc_counter', array( 'type' => 'integer', 'default' => 0 ) );
	}

	public function testmeboo()
	{
		add_action('send_monthly_sfc_cronjob', 'send_sfc_mail');
	}

}
