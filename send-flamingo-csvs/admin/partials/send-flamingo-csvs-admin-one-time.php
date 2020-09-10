<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://abdelrahmanma.com
 * @since      1.0.0
 *
 * @package    send_flamingo_csvs
 * @subpackage send_flamingo_csvs/admin/partials
 */

?>

<div class="wrapper">
    <h1>One Time Email With CSV</h1>
    <div id="admin_notice_sent" class="d-none notice notice-success is-dismissible mb-4">
        <p>Email Was Sent Successfully.</p>
    </div>
    <div class="postbox">
        <div class="inside">
            <div class="row mb-4 mt-4">
                <div class="form-group col-6">
                    <label for="to_email">To Email</label>
                    <input required type="email" id="to_email" name="to_email" class="form-control" " placeholder="Enter Email" />
                </div>
                <div class="form-group col-6">
                    <label for="email_subject">Email Subject</label>
                    <input required type="text" id="email_subject" name="email_subject" class="form-control" placeholder="Enter Email Subject"  autocomplete="none"/>
                </div>
            </div>
            <div class="row mt-4 mb-4">
                <div class="form-group col-6">
                    <label for="filter-by-date">Submissions Date</label>
                    <?php sfc_get_flamingo_months();?>
                </div>
            </div>
            <div class="row mt-4 mb-4">
                <div class="form-group col-12">
                    <label for="email_content">Content</label>
                    <?php wp_editor( '', 'email_content', array( 'media_buttons' => false, 'wpautop' => false ) );?>
                    <!-- <textarea id="email_content" class="form-control rounded-2" name="email_content" rows="10"></textarea> -->
                </div>
            </div>
            <button id="send_onetime_email" class="btn btn-primary" disabled>Send Email</button>
        </div>
    </div>
    <div id="admin_notice_fail" class="d-none notice notice-error is-dismissible mb-4">
        <p>Something Went Wrong.</p>
    </div>
</div>

<script>
    (function($){
        $(document).on('ready', function(e){
            let $to_email = $('#to_email');
            let $email_subject = $('#email_subject');
            let $email_content = $('#email_content');
            let $send_onetime_email = $('#send_onetime_email');
            let $date = $('#filter-by-date');
            for( let $comp of [$to_email, $email_subject]){
                $comp.on('keydown keyup change', function(e){
                    if($to_email.val() != '' && $email_subject.val() != ''){
                        $send_onetime_email.prop({
                            disabled: false
                        });
                    }
                    else{
                        $send_onetime_email.prop({
                            disabled: true
                        });
                    }
                });
            }
            $send_onetime_email.on('click', function(e){
                $send_onetime_email.prop({
                    disabled: true
                });
                let data = {
                    action: 'sfc_send_one_time_mail',
                    to_email: $to_email.val(),
                    email_subject: $email_subject.val(),
                    email_content: tinyMCE.activeEditor.getContent(),
                    date : $date.val()
                };
                $.post("<?= admin_url('admin-ajax.php'); ?>", data)
                    .done(function(response){
                        if(response === '1'){
                            $('#admin_notice_sent').removeClass('d-none');

                            $to_email.val('');
                            $email_subject.val('');
                            $email_content.val('');
                            tinyMCE.activeEditor.setContent('');
                            $date.val('0');
                            $(window).scrollTop(0);
                        } else {
                            $('#admin_notice_fail').removeClass('d-none');
                            $send_onetime_email.prop({
                                disabled: false
                            });
                        }
                        console.log(response);
                    });
            });
        });
    })(jQuery);
</script>