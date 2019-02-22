<?php

namespace Municipio\Controller;

class Page extends \Municipio\Controller\BaseController
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
}
