<?php

namespace Intranet\Admin;

class AuthorMetaBox
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'metaboxTitle'));
        add_filter('default_hidden_meta_boxes', array($this, 'alwaysShowAuthorMetabox'), 10, 2);
    }

    /**
     * Changes the metabox title of the author metabox (admin)
     * @return void
     */
    public function metaboxTitle()
    {
        global $wp_meta_boxes;
        if (!isset($wp_meta_boxes['page']['normal']['core']['authordiv']['title'])) {
            return;
        }

        $wp_meta_boxes['page']['normal']['core']['authordiv']['title'] = __('Page manager', 'municipio-intranet');
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
            return;
        }

        $hidden = array_filter($hidden, function ($item) {
            return $item != 'authordiv';
        });

        return $hidden;
    }
}
