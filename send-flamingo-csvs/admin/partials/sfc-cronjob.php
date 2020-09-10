<?php
defined('ABSPATH') or die('You can\'t access this file.');

if( !function_exists( 'sfc_cpt_custom_fields' ) ) {
    add_action( 'add_meta_boxes', 'sfc_cronjob_custom_fields', 10, 2 );
    function sfc_cronjob_custom_fields( $post_type, $post )
    {
        if ( sfc_cpt_status( 'edit' ) && 'publish' === $post->post_status ) {
            add_meta_box( 'sfc_operations_meta', 'Cronjob Operations', 'sfc_operations_meta_html', 'sfc_cronjob', 'side', 'low' );
        }
        add_meta_box( 'sfc_settings_meta', 'Cronjob Settings', 'sfc_settings_meta_html', 'sfc_cronjob', 'normal', 'low' );
    }
}

if( !function_exists( 'sfc_operations_meta_html' ) ) {
    function sfc_operations_meta_html( $post ) {
        $sched_running = get_post_meta($post->ID, '_sched_running_meta', true);
        if ( $sched_running == 'running' ) {
            $badge = 'success';
            $badge_text = 'Running';
        } else {
            $badge = 'danger';
            $badge_text = 'Not Running';
        }
        ?>
    <div class="form-group mt-3 mb-2">
        <span class="d-inline-block col-12" tabindex="0" data-toggle="tooltip" title="The Cronjob is <?= $badge_text; ?>">

            <span class="badge badge-<?= $badge; ?> col" style="font-size: 18px; line-height:1.3;"><?= $badge_text; ?></span>
        </span>
        <?php if ( $sched_running !== 'running' ) { ?>
            <span class="d-inline-block col-12" tabindex="0" data-toggle="tooltip" title="Start the cronjob">
                <button id="start_camp" class="btn btn-primary mt-2 col" <?= disabled( $sched_running, 'running' ) ?>>Start Cronjob
                </button>
            </span>
        <?php } else { ?>
            <span class="d-inline-block col-12" tabindex="0" data-toggle="tooltip" title="Stop the cronjob">

                <button id="stop_camp" class="btn btn-danger mt-2 col" <?= disabled( $sched_running, 'not_running' ) ?>>Stop Cronjob
                </button>
            </span>
        <?php } ?>
        <script>
            (function($) {
                $(document).ready(function() {
                    $('#start_camp').on('click', function(e) {
                        e.preventDefault();
                        $(this).attr('disabled', true);
                        var data = {
                            'action': 'sfc_cronjob_operations',
                            'operation': 'start',
                            'cron_id': <?= $post->ID ?>
                        };
                        $.when($.post("<?= admin_url('admin-ajax.php'); ?>", data)).done(function(x) {
                            window.location.reload();
                        });
                    });
                    $('#stop_camp').on('click', function(e) {
                        e.preventDefault();
                        $(this).attr('disabled', true);
                        var data = {
                            'action': 'sfc_cronjob_operations',
                            'operation': 'stop',
                            'cron_id': <?= $post->ID ?>
                        };
                        $.when($.post("<?= admin_url('admin-ajax.php'); ?>", data)).done(function(x) {
                            window.location.reload();
                        });
                    });
                });
            })(jQuery);
        </script>
    </div>
    <?php
    }
}

if( !function_exists( 'sfc_settings_meta_html' ) ) {
    function sfc_settings_meta_html ($post) {
        $to_email = get_post_meta($post->ID, '_to_email_meta', true);
        $email_subject = get_post_meta($post->ID, '_email_subject_meta', true);
        ?>
        <div class="row mb-4 mt-4">
            <div class="form-group col-6">
                <label for="to_email">To Email</label>
                <input required type="email" id="to_email" name="to_email" class="form-control" value="<?= $to_email; ?>" placeholder="Enter Email" />
            </div>
            <div class="form-group col-6">
                <label for="email_subject">Email Subject</label>
                <input required type="text" id="email_subject" name="email_subject" class="form-control" value="<?= $email_subject; ?>" placeholder="Enter Email Subject"  autocomplete="none"/>
            </div>
        </div>
        <?php
    }
}

if( !function_exists( 'sfc_cronjob_save_postdata' ) ) {

    add_action('save_post_sfc_cronjob', 'sfc_cronjob_save_postdata', 10, 3);

    function sfc_cronjob_save_postdata($post_id, $post, $update) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

        if ( isset( $_POST['post_type'] ) && 'sfc_cronjob' == $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( !current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        }

        if ( array_key_exists( 'to_email', $_POST ) ) {
            update_post_meta(
                $post_id,
                '_to_email_meta',
                $_POST['to_email']
            );
        }

        if ( array_key_exists( 'email_subject', $_POST ) ) {
            update_post_meta(
                $post_id,
                '_email_subject_meta',
                $_POST['email_subject']
            );
        }
    }
}

if( !function_exists( 'sfc_cronjob_columns' ) ) {

    add_filter('manage_edit-sfc_cronjob_columns', 'sfc_cronjob_columns');

    function sfc_cronjob_columns($columns) {

        $columns = array(
            'cb' => '&lt;input type="checkbox" />',
            'title' => __('Title'),
            'receiver' => __('Receiver'),
            'subject' => __('Subject'),
            'next_time' => __('Scheduled On'),
            'status' => __('Status'),
            'date' => __('Date')
        );

        $status = get_query_var( 'post_status' );
        if ( 'trash' === $status ) {
            unset($columns['next_time']);
            unset($columns['status']);
        }

        return $columns;
    }

}

if( !function_exists('sfc_cronjob_manage_columns') ){

    add_action('manage_sfc_cronjob_posts_custom_column', 'sfc_cronjob_manage_columns', 10, 2);
    function sfc_cronjob_manage_columns($column, $post_id) {

        switch ($column) {
            case "receiver":
                $to_email = get_post_meta($post_id, '_to_email_meta', true);
                echo $to_email;
                break;
            case "subject":
                $email_subject = get_post_meta($post_id, '_email_subject_meta', true);
                echo $email_subject;
                break;
            case "next_time":
                $next_time = get_post_meta($post_id, '_next_time_meta', true);
                if(empty($next_time) && $next_time !== '0'){
                    echo '-';
                }
                else {
                    $next_time = DateTime::createFromFormat('U', $next_time);
                    echo $next_time->format('M jS, gA');
                }
                break;
            case "status":
                $sched_running = get_post_meta($post_id, '_sched_running_meta', true);
                if ($sched_running == 'running') {
                    $badge_text = 'Running';
                    $badge = 'success';
                } else {
                    $badge_text = 'Not Running';
                    $badge = 'danger';
                }
                printf('<span class="badge badge-%s" style="font-size: 14px; line-height:1.3;">%s</span>', $badge, $badge_text);
                break;
        }
    }
}

if( !function_exists('sfc_cronjob_sortable_columns') ) {

    add_filter('manage_edit-sfc_cronjob_sortable_columns', 'sfc_cronjob_sortable_columns');

    function sfc_cronjob_sortable_columns($columns)
    {
        $columns['receiver'] = 'receiver';
        $columns['subject'] = 'subject';
        $columns['next_time'] = 'next_time';
        $columns['status'] = 'status';
        return $columns;
    }

}

if( !function_exists('sfc_cronjob_hide_quick_edit') ) {

    add_filter('post_row_actions', 'sfc_cronjob_hide_quick_edit', 10, 2);

    function sfc_cronjob_hide_quick_edit($actions, $post)
    {
        if ('sfc_cronjob' === $post->post_type) {
            unset($actions['inline hide-if-no-js']);
        }

        return $actions;
    }
}