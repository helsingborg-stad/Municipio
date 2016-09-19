<?php

namespace Intranet\Admin;

class AuthorMetaBox
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'authorDiv'));
        add_filter('default_hidden_meta_boxes', array($this, 'alwaysShowAuthorMetabox'), 10, 2);
    }

    /**
     * Changes the metabox title of the author metabox (admin)
     * @return void
     */
    public function authorDiv()
    {
        //__('Page manager', 'municipio-intranet')
        foreach (get_post_types() as $postType) {
            if (!post_type_supports($postType, 'author')) {
                continue;
            }

            remove_meta_box('authordiv', $postType, 'normal');
            add_meta_box(
                'authordiv',
                __('Page manager', 'municipio-intranet'),
                array($this, 'authorDivContent'),
                $postType,
                'normal',
                'default'
            );
        }
    }

    public function authorDivContent()
    {
        global $post;

        $authors = get_users(array(
            'who' => 'authors'
        ));

        uasort($authors, function ($a, $b) use ($post) {
            if ($post->post_author == $a->ID) {
                return -1;
            }

            if ($post->post_author == $b->ID) {
                return 1;
            }

            return 0;
        });

        include INTRANET_TEMPLATE_PATH . 'admin/authordiv.php';
    }

    /**
     * Display the author metabox by default
     * @param  array $hidden Hidden metaboxes before
     * @param  array $screen Screen args
     * @return array         Hidden metaboxes after
     */
    public function alwaysShowAuthorMetabox($hidden, $screen)
    {
        if ($screen->post_type != 'page') {
            return $hidden;
        }

        $hidden = array_filter($hidden, function ($item) {
            return $item != 'authordiv';
        });

        return $hidden;
    }
}
