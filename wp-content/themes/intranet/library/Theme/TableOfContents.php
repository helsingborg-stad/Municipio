<?php

namespace Intranet\Theme;

class TableOfContents
{
    public function __construct()
    {
        add_action('init', array($this, 'urlRewrite'));
        add_filter('template_include', array($this, 'template'), 10);
        add_filter('pre_get_posts', function ($query) {
            if (!isset($query->query['table-of-contents'])) {
                return $query;
            }

            $query->is_home = false;
            return $query;
        });
    }

    public function urlRewrite()
    {
        add_rewrite_rule('^table-of-contents', 'index.php?table-of-contents&modularity_template=table-of-contents', 'top');
        add_rewrite_tag('%table-of-contents%', '([^&]+)');
        flush_rewrite_rules();
    }

    public function template($template)
    {
        global $wp_query;

        if (!isset($wp_query->query['table-of-contents'])) {
            return $template;
        }

        $template = \Municipio\Helper\Template::locateTemplate('table-of-contents');
        return $template;
    }
}
