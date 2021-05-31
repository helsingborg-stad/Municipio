<?php

namespace Municipio\Comment;
use \HelsingborgStad\RecaptchaIntegration as Captcha;

/**
 * Class CommentsFilters
 * @package Municipio\Comment
 */
class CommentsFilters
{
    /**
     * CommentsFilters constructor.
     */
    public function __construct()
    {
        add_action('pre_comment_on_post', array($this, 'reCaptchaValidation'));
        add_filter('comment_text', array($this, 'stripTags'), 10, 2);
    }

    /**
     * Strip html from comment
     * @param $comment_text
     * @param $comment
     * @return array
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
     * Check reCaptcha Keys
     */
    public function reCaptchaValidation()
    {
        if (is_user_logged_in()) {
            return;
        }

        Captcha::initCaptcha();
    }
}
