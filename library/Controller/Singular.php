<?php

namespace Municipio\Controller;

class Singular extends \Municipio\Controller\BaseController
{
    public function init()
    {
        //Get post data 
        $this->data['post'] = \Municipio\Helper\Post::preparePostObject(get_post());

        //Comments
        $this->data['comments'] = get_comments(array(
            'post_id'   => $this->data['post']->id,
            'order'     => get_option('comment_order')
        ));

        //Replies
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

        //Post settings
        $this->data['settingItems'] = apply_filters_deprecated('Municipio/blog/post_settings', array($this->data['post']), '3.0', 'Municipio/blog/postSettings'); 

        //Should link author page
        $this->data['authorPages'] = apply_filters('Municipio/author/hasAuthorPage', false);
    }


    // TODO: ADD LIKE BUTTON. MOVE TO OWN CLASS/HELPER.

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
