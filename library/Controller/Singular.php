<?php

namespace Municipio\Controller;

class Singular extends \Municipio\Controller\BaseController
{
    public function init()
    {
        global $post;
        $this->data['comments'] = get_comments(array(
            'post_id'   => $post->ID,
            'order'     => get_option('comment_order')
        ));
        $this->data['replyArgs'] = array(
            'add_below'  => 'comment',
            'respond_id' => 'respond',
            'reply_text' => __('Reply'),
            'login_text' => __('Log in to Reply'),
            'depth'      => 1,
            'before'     => '',
            'after'      => '',
            'max_depth'  => get_option('thread_comments_depth')
        );
        $this->data['settingItems'] = apply_filters('Municipio/blog/post_settings', array(), $post);

        if (defined('MUNICIPIO_BLOCK_AUTHOR_PAGES') && ! MUNICIPIO_BLOCK_AUTHOR_PAGES) {
            $this->data['authorPages'] = true;
        }
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

        $output = '<a class="' . implode(' ', $classes) . '" href="javascript:void(0)" data-comment-id="' . $id . '">';
        $output .= '<span id="like-count">' . $count . '</span>';
        $output .= '</a>';

        return $output;
    }
}
