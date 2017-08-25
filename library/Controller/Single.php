<?php

namespace Municipio\Controller;

class Single extends \Municipio\Controller\BaseController
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
    }

    public static function likeButton($id)
    {
        $likes = get_comment_meta( $id, '_likes', true );

        if(empty($likes) || is_array($likes) == false) {
            $count = 0;
        }
        else {
            $count = count($likes);
        }

        $classes = array('like-button');

        if(is_array($likes) == true && in_array(get_current_user_id(), $likes)) {
            $classes[] = 'active';
        }

        $output = '<a class="' . implode(' ', $classes) . '" href="#" data-comment-id="' . $id . '">';
        $output .= '<span id="like-count">' . $count . '</span>';
        $output .= '</a>';

        return $output;
    }
}
