<?php

namespace Municipio\Comment;

class Likes extends \Municipio\Helper\Ajax
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
        $commentObj = get_comment($commentId);

        $like = array();
        $create = true;

        if (is_array(get_comment_meta($commentId, '_likes', true)) == true) {
            $like = array_merge($like, get_comment_meta($commentId, '_likes', true));
        }
        if (in_array(get_current_user_id(), $like)) {
            $create = false;
            $index = array_search(get_current_user_id(), $like);
            unset($like[$index]);
        } else {
            $like[] = get_current_user_id();
        }

        do_action('Municipio/comment/save_like', $commentObj, get_current_user_id(), $create);
        update_comment_meta($commentId, '_likes', $like);

        return true;
    }

    /**
     * Display comment like button
     * @param int $id Comment ID
     * @return string Markup to display button
     */
    public static function likeButton($id)
    {
        if (! is_user_logged_in()) {
            return;
        }

        $likes = get_comment_meta($id, '_likes', true);
        if (empty($likes) || is_array($likes) == false) {
            $count = 0;
        } else {
            $count = count($likes);
        }

        $classes = array('like-button');

        if (is_array($likes) == true && in_array(get_current_user_id(), $likes)) {
            $classes[] = 'active';
        }

        $output['classList'] = implode(' ', $classes);
        $output['icon'] = (strpos($output['classList'], 'active')) ? 'thumb_down'
            : 'thumb_up';
        $output['text'] =  (strpos($output['classList'], 'active')) ? __('Dislike ','municipio')
            : __('Like ', 'municipio');
        $output['count'] = $count;

        return $output;
    }
}
