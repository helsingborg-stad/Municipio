<?php

namespace Municipio\Comment;

class CommentsFilters
{
    public function __construct()
    {
        add_action('pre_comment_on_post', array($this, 'validateReCaptcha'));
        add_action('admin_notices', array($this, 'recaptchaConstant'));
        add_filter('comment_text', array($this, 'stripTags'), 10, 2);
    }

    /**
     * Strip html from comment
     */
    public function stripTags($comment_text, $comment)
    {
        $allowedTags = array(
            "<h1>", "<h2>", "<h3>", "<h4>",
            "<strong>","<b>",
            "<br>", "<hr>",
            "<em>",
            "<ol>","<ul>","<li>",
            "<p>", "<span>", "<a>", "<img>",
            "<del>", "<ins>",
            "<blockquote>"
        );

        $allowedAttributes = array('href', 'class', 'rel', 'id', 'src');

        return \Municipio\Helper\Html::stripTagsAndAtts($comment_text, $allowedTags, $allowedAttributes);
    }

    /**
     * Add admin notice if Google reCaptcha constants is missing
     */
    public function recaptchaConstant()
    {
        if (defined('G_RECAPTCHA_KEY') && defined('G_RECAPTCHA_SECRET')) {
            return;
        }
        $class = 'notice notice-warning';
        $message = __('Municipio: constant \'g_recaptcha_key\' or \'g_recaptcha_secret\' is not defined.', 'municipio');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    /**
     * Check reCaptcha before comment is saved to post
     * @return void
     */
    public function validateReCaptcha()
    {
        if (is_user_logged_in()) {
            return;
        }

        $response = isset($_POST['g-recaptcha-response']) ? esc_attr($_POST['g-recaptcha-response']) : '';
        $reCaptcha = \Municipio\Helper\ReCaptcha::controlReCaptcha($response);

        if (!$reCaptcha) {
            wp_die(sprintf('<strong>%s</strong>:&nbsp;%s', __('Error', 'municipio'), __('reCaptcha verification failed', 'municipio')));
        }

        return;
    }
}
