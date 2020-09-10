<?php
// If this file is called directly, abort.
defined('ABSPATH') or die('You can\'t access this file.');

if ( !function_exists( 'sfc_cronjob_operations' ) ) {
    add_action( 'wp_ajax_sfc_cronjob_operations', 'sfc_cronjob_operations' );
    function sfc_cronjob_operations()
    {
        if (isset( $_POST['operation'])) {
            $operation = $_POST['operation'];
        } else {
            $operation = FALSE;
        }
        if (isset( $_POST['cron_id'])) {
            $cron_id = $_POST['cron_id'];
        } else {
            $cron_id = FALSE;
        }
        if ( $operation === 'start' && $cron_id) {
            sfc_start_cronjob( $cron_id );
        } elseif ( $operation === 'stop' && $cron_id) {
            sfc_stop_cronjob( $cron_id );
        }
    }
}
if ( !function_exists( 'sfc_stop_cronjob' ) ) {
    function sfc_start_cronjob( $cron_id )
    {
        $timezone = wp_timezone()->getName();

        $next_time = strtotime( 'first day of next month 7AM ' . $timezone );

        wp_schedule_single_event( $next_time, "send_monthly_sfc_cronjob", array( (int) $cron_id ) );

        update_post_meta(
            $cron_id,
            '_sched_running_meta',
            'running'
         );
         update_post_meta(
            $cron_id,
            '_next_time_meta',
            $next_time
         );

    }
}
if ( !function_exists( 'sfc_stop_cronjob' ) ) {
    function sfc_stop_cronjob( $cron_id )
    {
        wp_clear_scheduled_hook( "send_monthly_sfc_cronjob", array( (int) $cron_id ) );

        update_post_meta(
            $cron_id,
            '_sched_running_meta',
            'not_running'
         );
         update_post_meta(
            $cron_id,
            '_next_time_meta',
            ''
         );
    }
}

if ( !function_exists( 'on_transition_sfc_cronjob' ) ) {

    add_action( 'transition_post_status', 'on_transition_sfc_cronjob', 10, 3 );

    function on_transition_sfc_cronjob( $new_status, $old_status, $post)
    {
        if ( $new_status == $old_status || 'sfc_cronjob' !== $post->post_type ) {
            return;
        }

        if ( ( 'publish' === $new_status && 'publish' !== $old_status ) ) {
            sfc_start_cronjob( (int)$post->ID );
        } else if( 'publish' === $old_status ){
            sfc_stop_cronjob( $post->ID );
        }
    }
}
