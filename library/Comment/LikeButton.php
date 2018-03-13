<?php

namespace Municipio\Comment;

class LikeButton extends \Municipio\Helper\Ajax
{
    public function __construct()
    {
        //Data
        $this->data['ajax_url'] = admin_url('admin-ajax.php');
        $this->data['nonce'] = wp_create_nonce('likeNonce');

        //Localize
        $this->localize('likeButtonData');

        //Hook method to ajax
        $this->hook('ajaxLikeMethod', true);
    }

    /**
     * Ajax method to add comment likes
     * @return boolean
     */
    public function ajaxLikeMethod()
    {
        if (! defined('DOING_AJAX') && ! DOING_AJAX) {
            return false;
        }

        if (! wp_verify_nonce($_POST['nonce'], 'likeNonce')) {
            die('Busted!');
        }

        ignore_user_abort(true);

        $commentId = $_REQUEST['comment_id'];
        $like = array();

        if (is_array(get_comment_meta($commentId, '_likes', true)) == true) {
            $like = array_merge($like, get_comment_meta($commentId, '_likes', true));
        }
        if (in_array(get_current_user_id(), $like)) {
            $index = array_search(get_current_user_id(), $like);
            unset($like[$index]);
        } else {
            $like[] = get_current_user_id();
        }

        update_comment_meta($commentId, '_likes', $like);

        return true;
    }
}
