<?php

namespace Municipio\Theme;

class Blog
{
    public function __construct()
    {
        add_action('wp', array($this, 'checkIfBlogIsPrivate'));
    }

    public function checkIfBlogIsPrivate()
    {
        $blogPage = get_option('page_for_posts');

        if (!$blogPage) {
            return;
        }

        $blogPage = get_page($blogPage);

        if ($blogPage->post_status === 'private' && !is_user_logged_in()) {
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
        }

        return;
    }
}
