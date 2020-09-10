<?php
defined('ABSPATH') or die('You can\'t access this file.');

if (!function_exists('sfc_cronjob_cpt')) {

	add_action('init', 'sfc_cronjob_cpt', 10);
	function sfc_cronjob_cpt()
	{
		$labels = array(
			'name'                => _x('Cronjobs', 'Post Type General Name', 'send-flamingo-csvs'),
			'singular_name'       => _x('Cronjob', 'Post Type Singular Name', 'send-flamingo-csvs'),
			'menu_name'           => __('Cronjobs', 'send-flamingo-csvs'),
			'parent_item_colon'   => __('Parent Cronjob:', 'send-flamingo-csvs'),
			'all_items'           => __('Cronjobs', 'send-flamingo-csvs'),
			'view_item'           => __('View Cronjob', 'send-flamingo-csvs'),
			'add_new_item'        => __('Add New Cronjob', 'send-flamingo-csvs'),
			'add_new'             => __('New Cronjob', 'send-flamingo-csvs'),
			'edit_item'           => __('Edit Cronjob', 'send-flamingo-csvs'),
			'update_item'         => __('Update Cronjob', 'send-flamingo-csvs'),
			'search_items'        => __('Search Cronjobs', 'send-flamingo-csvs'),
			'not_found'           => __('No Cronjobs found', 'send-flamingo-csvs'),
			'not_found_in_trash'  => __('No Cronjobs found in trash', 'send-flamingo-csvs'),
		);
		$args = array(
			'label'               => __('Cronjob', 'send-flamingo-csvs'),
			'description'         => __('Cronjobs', 'send-flamingo-csvs'),
			'labels'              => $labels,
			'supports'            => array('title', 'editor'),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'		  => 'send-flamingo-csvs-dashboard',
			'show_in_admin_bar'   => true,
			'menu_position'       => 10,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'query_var'			  => false,
		);
		register_post_type('sfc_cronjob', $args);
	}
}
