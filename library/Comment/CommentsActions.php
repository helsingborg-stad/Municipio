<?php

namespace Municipio\Comment;

class CommentsActions
{
    public function __construct()
    {
        add_action('wp_ajax_remove_comment', array($this, 'removeComment'));
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
        if (!check_ajax_referer("delete-comment_$id", '_ajax_nonce', false)) {
            wp_send_json_error('Nonce failed');
        }

        $trashed = wp_trash_comment($comment);
        if ($trashed) {
            wp_send_json_success('Deletion was successful');
        }

        wp_send_json();
    }
}
