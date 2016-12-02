<?php

namespace Intranet\Helper;

class FragmentCache
{
    public function __construct()
    {
        add_action('save_post', array($this, 'banNewsCache'));
    }

    public function banNewsCache($post_id)
    {

        if (!function_exists('get_sites')) {
            return;
        }

        if (wp_is_post_revision($post_id)) {
            return;
        }

        if (get_post_type($post_id) == 'intranet-news') {
            foreach (get_sites() as $site) {
                switch_to_blog($site->blog_id);
                wp_cache_delete('intranet_news');
                restore_current_blog();
            }
        }
    }
}
