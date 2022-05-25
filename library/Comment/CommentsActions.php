<?php

namespace Municipio\Comment;

/**
 * Class CommentsActions
 * @package Municipio\Comment
 */
class CommentsActions
{
    public function __construct()
    {
        add_action('wp_ajax_remove_comment', array($this, 'removeComment'));
        add_action('wp_ajax_get_comment_form', array($this, 'getCommentForm'));
        add_action('wp_ajax_update_comment', array($this, 'updateComment'));

        add_filter('cancel_comment_reply_link', array($this, 'replaceCommentCancelLink'), 10, 3);
    }

    public function replaceCommentCancelLink($markup, $link, $text) {
        return render_blade_view('partials.comments.cancel-button', [
            'link' => $link,
            'text' => $text
        ]);
    }

    /**
     * Update a comment front end
     * @return void
     */
    public function updateComment()
    {
        $newComment = $_POST['comment'] ?? null;
        $commentId = $_POST['commentId'] ?? null;

        if (!$newComment || !$commentId || !$comment = get_comment($commentId)) {
            wp_send_json_error('Missing variables');
        }

        if (!current_user_can('edit_comment', $comment->comment_ID) && !($comment->user_id == get_current_user_id())) {
            wp_send_json_error('Missing authorization');
        }

        // Validate nonce
        if (!check_ajax_referer("update-comment_$comment->comment_ID", 'nonce', false)) {
            wp_send_json_error('Nonce failed');
        }

        $comment->comment_content = $newComment;
        if (wp_update_comment((array) $comment)) {
            wp_send_json_success('Update was successful');
        }

        wp_send_json();
    }

    /**
     * Returns markup for the edit comment form
     * @return void
     */
    public function getCommentForm()
    {
        $postId = $_POST['postId'] ?? null;
        $commentId = $_POST['commentId'] ?? null;

        if (!$commentId || !$postId || !$comment = get_comment($commentId)) {
            wp_send_json_error('Missing variables');
        }

        if (!$comment = get_comment($commentId)) {
            wp_send_json_error('Comment is missing');
        }

        $args = array(
            'id_form' => 'commentupdate',
            'class_submit' => 'btn btn-sm btn-primary',
            'title_reply' => '',
            'title_reply_before' => '',
            'title_reply_after' => '',
            'label_submit' => __('Update', 'municipio'),
            'logged_in_as' => '',
            'comment_field' => '<textarea id="update-comment" name="comment" cols="45" rows="8" aria-required="true">' . $comment->comment_content . '</textarea>',
            'comment_notes_after' => '<input type="hidden" name="commentId" value="' . $commentId . '">
            <input type="hidden" name="nonce" value="' . wp_create_nonce("update-comment_$commentId") . '">',
            'submit_button' => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" /> <a href="#" class="cancel-update-comment gutter gutter-left gutter-sm"><small>' . __('Cancel', 'municipio') . '</small></a>'
        );

        ob_start();
        comment_form($args, $postId);
        $form = ob_get_clean();
        $form = str_replace('class="comment-respond"', 'class="comment-respond comment-respond-new comment-update"', $form);
        $form = str_replace('id="respond"', 'id="respond-edit"', $form);

        wp_send_json_success($form);
    }

    /**
     *
     */
    public static function getInitialCommentForm()
    {
        $data['lang'] = (object) [
            'comments' => __("Comment", "municipio"),
            'name' => __("Your name", "municipio"),
            'email' => __("Your email", "municipio"),
            'heading' => __("Leave a comment", "municipio")
        ];

        $args = array(
            'title_reply_before'    => '',
            'title_reply_after'     => '',
            'title_reply'           => '',
            'comment_notes_before'  => render_blade_view('partials.comments.before', $data),
            'comment_notes_after'   => render_blade_view('partials.comments.after', $data),
            'format'                => 'html5',
            'id_form'               => 'commentform',
            'class_form'            => 'c-paper c-paper--padding-3 c-form o-grid o-grid--form',
            'submit_field'          => '%1$s %2$s',

            'id_submit'             => 'submit',
            'class_submit'          => 'comment-reply-link',
            'name_submit'           => 'submit',
            'submit_button'         => render_blade_view('partials.comments.submit-button', $data),

            'cancel_reply_link'     => __('Cancel reply', 'municipio'),
            'cancel_reply_before'   => '',
            'cancel_reply_after'    => '',

            'comment_field'         => render_blade_view('partials.comments.field-comment', $data),
            'fields' => [
                'author'            => render_blade_view('partials.comments.field-author', $data),
                'email'             => render_blade_view('partials.comments.field-email', $data),
                'cookies'           => false, //Avoid cookies
                'url'               => false, //No need for url
            ],
            'must_log_in'           => render_blade_view('partials.comments.login-required', $data),
            'logged_in_as'          => false,
        );

        comment_form($args);
    }

    /**
     * Delete comment. Works similar as 'wp_ajax_delete_comment',
     * but this allows all user roles to delete their own comments.
     * @return void
     */
    public function removeComment()
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if (!$comment = get_comment($id)) {
            wp_send_json_error('Comment is missing');
        }

        if (!current_user_can('edit_comment', $comment->comment_ID) && !($comment->user_id == get_current_user_id())) {
            wp_send_json_error('Missing authorization');
        }

        // Validate nonce
        if (!check_ajax_referer("delete-comment_$id", 'nonce', false)) {
            wp_send_json_error('Nonce failed');
        }

        $trashed = wp_trash_comment($comment);
        if ($trashed) {
            wp_send_json_success('Deletion was successful');
        }

        wp_send_json();
    }
}
