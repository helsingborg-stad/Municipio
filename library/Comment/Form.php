<?php

namespace Municipio\Comment;

/**
 * Class Form
 * @package Municipio\Comment
 */
class Form
{
    public function __construct()
    {
        add_filter('cancel_comment_reply_link', array($this, 'replaceCommentCancelLink'), 10, 3);
    }

    /**
     * Filter comment form cancel link
     * Cancel button cannot be manpulated by attributes
     *
     * @return string HTML string containing markup for cancel comment button
     */
    public function replaceCommentCancelLink($markup, $link, $text)
    {
        return render_blade_view('partials.comments.cancel-button', [
            'link' => $link,
            'text' => $text
        ]);
    }

    /**
     *  Get component driven comment form
     *  @return void
     */
    public static function get() //:void
    {
        $data['lang'] = (object) [
            'comments'      => __("Comment", 'municipio'),
            'name'          => __("Your name", 'municipio'),
            'email'         => __("Your email", 'municipio'),
            'heading'       => __("Leave a comment", 'municipio'),
            'loginrequired' => __("You need to login before leaving a comment.", 'municipio'),
        ];

        $args = array(
            'title_reply_before'   => '',
            'title_reply_after'    => '',
            'title_reply'          => '',
            'comment_notes_before' => render_blade_view('partials.comments.before', $data),
            'comment_notes_after'  => render_blade_view('partials.comments.after', $data),
            'format'               => 'html5',
            'id_form'              => 'commentform',
            'class_form'           => 'c-form o-grid o-grid--form js-form-validation',
            'submit_field'         => '%1$s %2$s',

            'id_submit'            => 'submit',
            'class_submit'         => 'comment-reply-link',
            'name_submit'          => 'submit',
            'submit_button'        => render_blade_view('partials.comments.submit-button', $data),

            'cancel_reply_link'    => __('Cancel reply', 'municipio'),
            'cancel_reply_before'  => '',
            'cancel_reply_after'   => '',

            'comment_field'        => render_blade_view('partials.comments.field-comment', $data),
            'fields'               => [
                'author'  => render_blade_view('partials.comments.field-author', $data),
                'email'   => render_blade_view('partials.comments.field-email', $data),
                'cookies' => false, //Avoid cookies
                'url'     => false, //No need for url
            ],
            'must_log_in'          => render_blade_view('partials.comments.login-required', $data),
            'logged_in_as'         => false,
        );

        comment_form($args);
    }
}
