<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://abdelrahmanma.com
 * @since      1.0.0
 *
 * @package    send_flamingo_csvs
 * @subpackage send_flamingo_csvs/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    send_flamingo_csvs
 * @subpackage send_flamingo_csvs/includes
 * @author     Abdelrahman Muhammad <contact@abdelrahmanma.com>
 */
class send_Flamingo_Csvs_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$query = new WP_Query(array(
			'post_type' => 'sfc_cronjob',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		));

		while ($query->have_posts()) {
			$query->the_post();
			$cron_id = get_the_ID();
			sfc_stop_cronjob($cron_id);
		}
		flush_rewrite_rules();
	}

}
