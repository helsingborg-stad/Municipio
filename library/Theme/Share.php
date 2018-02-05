<?php

namespace Municipio\Theme;

class Share
{
    public function __construct()
    {
        add_action('wp_ajax_share_email', array($this, 'socialShareEmail'));
        add_action('wp_ajax_nopriv_share_email', array($this, 'socialShareEmail'));
    }

    /**
     * Share a post by email
     * @return void
     */
    public function socialShareEmail()
    {

        //Should show recaptcha?
        if (!is_user_logged_in() && defined('G_RECAPTCHA_KEY')) {
            $response   = isset($_POST['g-recaptcha-response']) ? esc_attr($_POST['g-recaptcha-response']) : '';
            $reCaptcha = \Municipio\Helper\ReCaptcha::controlReCaptcha($response);

            if (!$reCaptcha) {
                wp_send_json_error(__('Something went wrong, please try again', 'municipio'));
            }
        }

        //Get user data
        $user        = wp_get_current_user();
        $postId      = isset($_POST['post_id']) ? $_POST['post_id'] : null;
        $senderName  = is_user_logged_in() ? $user->display_name : (isset($_POST['sender_name']) ? $_POST['sender_name'] : "");
        $senderEmail = is_user_logged_in() ? $user->user_email : (isset($_POST['sender_email']) ? $_POST['sender_email'] : "");
        $recipient   = isset($_POST['recipient_email']) ? $_POST['recipient_email'] : null;

        $message     = '<a href="' . get_permalink($postId) . '" target="_blank">' . get_permalink($postId) . '</a>';
        $message    .= !empty($_POST['message']) ? '<br><br>' . $_POST['message'] : '';
        $message    .= '<br><br>---<br>' . sprintf(__('This message was sent by %s via %s', 'municipio'), $senderEmail, '<a href="' . get_site_url() . '" target="_blank">' . get_site_url() . '</a>');

        // Send the email
        $mail = wp_mail(
            $recipient,
            $senderName . ' ' . __('sent you a link via', 'municipio') . ': ' . get_site_url(),
            $message,
            array(
                'From: ' . $senderName . ' <' . $senderEmail . '>',
                'Content-Type: text/html; charset=UTF-8'
            )
        );

        wp_send_json_success(__('The message was sent successfully', 'municipio'));
    }
}
