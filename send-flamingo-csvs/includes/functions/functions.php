<?php
// If this file is called directly, abort.
defined('ABSPATH') or die('You can\'t access this file.');

if ( !function_exists( 'sfc_cpt_status' ) ) {
    function sfc_cpt_status( $new_edit = null ) {
        global $pagenow;
        //make sure we are on the backend
        if ( !is_admin() ) return false;


        if ( $new_edit == "edit" )
            return in_array( $pagenow, array( 'post.php', ) );
        elseif ( $new_edit == "new" ) //check for new post page
            return in_array( $pagenow, array('post-new.php') );
        else //check for either new or edit
            return in_array( $pagenow, array('post.php', 'post-new.php' ) );
    }
}

if( !function_exists( 'sfc_get_flamingo_months' ) ) {
    function sfc_get_flamingo_months(){
        global $wpdb, $wp_locale;

        $post_type = Flamingo_Inbound_Message::post_type;
        $extra_checks = "AND post_status != 'auto-draft'";
        if ( ! isset( $_GET['post_status'] ) || 'trash' !== $_GET['post_status'] ) {
            $extra_checks .= " AND post_status != 'trash'";
        } elseif ( isset( $_GET['post_status'] ) ) {
            $extra_checks = $wpdb->prepare( ' AND post_status = %s', $_GET['post_status'] );
        }

        $months = $wpdb->get_results(
            $wpdb->prepare(
                "
            SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
            FROM $wpdb->posts
            WHERE post_type = %s
            $extra_checks
            ORDER BY post_date DESC
        ",
                $post_type
            )
        );

        $months = apply_filters( 'months_dropdown_results', $months, $post_type );

        $month_count = count( $months );

        if ( ! $month_count || ( 1 == $month_count && 0 == $months[0]->month ) ) {
            return;
        }

        $m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
        ?>
        <label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
        <select name="m" id="filter-by-date" class="form-control">
            <option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates' ); ?></option>
        <?php
        foreach ( $months as $arc_row ) {
            if ( 0 == $arc_row->year ) {
                continue;
            }

            $month = zeroise( $arc_row->month, 2 );
            $year  = $arc_row->year;

            printf(
                "<option %s value='%s'>%s</option>\n",
                selected( $m, $year . $month, false ),
                esc_attr( $arc_row->year . $month ),
                /* translators: 1: Month name, 2: 4-digit year. */
                sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
            );
        }
        ?>
        </select>
        <?php
    }
}

if( !function_exists( 'get_csv_generated_by_flamingo' ) ) {
    function get_csv_generated_by_flamingo( $date = '0' ) {
        $args = array('m' => $date); // $date = '202003'

        $items = Flamingo_Inbound_Message::find( $args );

        if ( empty( $items ) ) {
            return '';
        }

        $labels = array_keys( $items[0]->fields );

        $generated_CSV =  flamingo_csv_row(
            array_merge( $labels, array( __( 'Date', 'flamingo' ) ) ) );

        foreach ( $items as $item ) {
            $row = array();

            foreach ( $labels as $label ) {
                $col = isset( $item->fields[$label] ) ? $item->fields[$label] : '';

                if ( is_array( $col ) ) {
                    $col = flamingo_array_flatten( $col );
                    $col = array_filter( array_map( 'trim', $col ) );
                    $col = implode( ', ', $col );
                }

                $row[] = $col;
            }

            $row[] = get_post_time( 'c', true, $item->id ); // Date

            $generated_CSV .= "\r\n" . flamingo_csv_row( $row );
        }
        return $generated_CSV;
    }
}

if( !function_exists( 'send_sfc_mail' ) ) {
    function send_sfc_mail( $cronjob ) {

        if( gettype($cronjob) == 'integer' ){

            $date = new DateTime();
            $month = str_pad($date->format('m') - 1, 2, '0', STR_PAD_LEFT);
            $year = $date->format('Y');
            $year_month = $year . '-' . $month;
            $date = $year . $month;

            $email = get_post_meta($cronjob, '_to_email_meta', true);
            $subject = get_post_meta($cronjob, '_email_subject_meta', true);
            $content = get_post_field('post_content', $cronjob);

            $timezone = wp_timezone()->getName();

            $next_time = strtotime( 'first day of next month 7AM ' . $timezone );

            wp_schedule_single_event( $next_time, "send_monthly_sfc_cronjob", array( (int) $cronjob ) );

            update_post_meta(
                $cronjob,
                '_next_time_meta',
                $next_time
             );

        } else if ( gettype($cronjob) == 'array' ) {

            $date = $cronjob['date'];
            if( $date != '0' ){
                $year = substr($date, 0, 4);
                $month = substr($date, 4);
                $year_month = $year  . '-' . $month;
            } else {
                $year_month = 'ALL';
            }

            $email = $cronjob['email'];
            $subject = $cronjob['subject'];
            $content = $cronjob['body'];
        }
        $directoryName = send_flamingo_csvs_dir.'includes/MonthlyCSVs';
        // //Check if the directory already exists.
        // if( !is_dir( $directoryName ) ){
        //     //Directory does not exist, so lets create it.
        //     if ( !mkdir( $directoryName, 0755 ) ) {
        //         error_log('could not create folder');
        //     }
        // }

        $filename = $directoryName.'/Monthly Submission ' . $year_month . '.csv';

        $csv_content = get_csv_generated_by_flamingo( $date );

		if(empty($csv_content)){
			$csv_content = 'No submissions found';
		}

        file_put_contents( $filename, $csv_content );

        $headers = array('Content-Type: text/html; charset=UTF-8');

        return wp_mail( $email, $subject, $content, $headers, $filename );

    }
}

if ( !function_exists( 'sfc_send_one_time_mail' ) ) {
    add_action( 'wp_ajax_sfc_send_one_time_mail', 'sfc_send_one_time_mail' );
    function sfc_send_one_time_mail()
    {
        $cronjob = array();

        if( isset( $_POST['to_email'] ) ){
            $cronjob['email'] = $_POST['to_email'];
        } else {
            echo 'false';
            wp_die();
        }


        if( isset( $_POST['email_subject'] ) ){
            $cronjob['subject'] = $_POST['email_subject'];
        } else {
            echo 'false';
            wp_die();
        }


        if( isset( $_POST['email_content'] ) ){
            $cronjob['body'] = stripslashes($_POST['email_content']);
        } else {
            echo 'false';
            wp_die();
        }


        if( isset( $_POST['date'] ) ){
            $cronjob['date'] = $_POST['date'];
        } else {
            echo 'false';
            wp_die();
        }

        echo send_sfc_mail($cronjob);
        wp_die();

    }
}

