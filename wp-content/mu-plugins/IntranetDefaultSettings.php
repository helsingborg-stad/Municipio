<?php

namespace IntranetDefaultSettings;

class IntranetDefaultSettings
{
    public function __construct()
    {
        // Blogs
        add_action('wpmu_new_blog', array($this, 'createFrontPage'));
        add_action('wpmu_new_blog', array($this, 'setDefaultSiteSettings'));
        add_action('wpmu_new_blog', array($this, 'searchWpSettings'));

        // Site list
        add_action('init', array($this, 'sitesListPageUrlRewrite'));
        add_filter('template_include', array($this, 'sitesListPageTemplate'), 10);
    }

    /**
     * Sets the default site settings
     * Add new settings with $key => $value structure
     * @param [type] $blogId [description]
     */
    public function setDefaultSiteSettings($blogId)
    {
        $defaultBlogOptions = array(

            // Color scheme
            'options_color_scheme'              => 'purple',

            // Header style
            'options_header_layout'             => 'intranet',

            // Show date published and date modified per default
            'options_show_date_updated'         => array('post', 'page', 'intranet-news'),
            'options_show_date_published'       => array('post', 'page', 'intranet-news'),

            // Show page author
            'options_page_show_author'          => 1,
            'options_page_show_author_image'    => 1,

            // Navigation settings (primary disabled, secondary enabled width settings)
            'options_nav_primary_enable'        => 0,
            'options_nav_sub_enable'            => 1,
            'options_nav_sub_type'              => 'auto',
            'options_nav_sub_depth'             => 0,
            'options_nav_sub_include_top_level' => 1,
            'options_nav_sub_render'            => 'active',

            // Show signature logo in footer
            'options_footer_signature_show'     => 1,

            // Search
            'options_search_placeholder_text'   => __('What are you looking for?', 'municipio-intranet')
        );

        foreach ($defaultBlogOptions as $key => $value) {
            update_blog_option($blogId, $key, $value);
        }
    }

    /**
     * Creates a static frontpage for the blog
     * @param  integer $blogId Blog id
     * @return void
     */
    public function createFrontPage($blogId)
    {
        switch_to_blog($blogId);

        // Delete "sample page"
        $defaultPage = get_page_by_title(__('Sample Page'));
        wp_delete_post($defaultPage->ID);

        // Add new front page
        $frontPageId = wp_insert_post(array(
            'post_type'   => 'page',
            'post_status' => 'publish',
            'post_title'  => __('Front page'),
            'menu_order'  => -1000
        ));

        update_blog_option($blogId, 'show_on_front', 'page');
        update_blog_option($blogId, 'page_on_front', $frontPageId);

        restore_current_blog();
    }

    /**
     * Adds default search engine to
     * @param  [type] $blogId [description]
     * @return [type]         [description]
     */
    public function searchWpSettings($blogId)
    {
        $defaultSearchWpOptions = array(
            'engines' => array(
                'default' => array(
                    'post' => array(
                        'enabled' => true,
                        'weights' => array(
                            'title' => 20,
                            'content' => 2,
                            'slug' => 10,
                            'tax' => array(
                                'category' => 5,
                                'post_tag' => 5
                            ),
                            'excerpt' => 6,
                            'comment' => 1
                        ),
                        'options' => array(
                            'exclude' => '',
                            'attribute_to' => ''
                        )
                    ),
                    'page' => array(
                        'enabled' => true,
                        'weights' => array ('title' => 20,
                            'content' => 2,
                            'slug' => 10,
                            'comment' => 1
                        ),
                        'options' => array(
                            'exclude' => '',
                            'attribute_to' => ''
                        )
                    ),
                    'intranet-news' => array(
                        'enabled' => true,
                        'weights' => array (
                            'title' => 20,
                            'content' => 2,
                            'slug' => 10
                        ),
                        'options' => array(
                            'exclude' => '',
                            'attribute_to' => ''
                        )
                    ),
                    'attachment' => array(
                        'enabled' => true,
                        'weights' => array(
                            'title' => 20,
                            'content' => 2,
                            'slug' => 10,
                            'excerpt' => 6,
                            'cf' => array (
                                'swpp574c370c1655b' => array(
                                    'metakey' => 'searchwp_content',
                                    'weight' => 2
                                )
                            )
                        ),
                        'options' => array(
                            'exclude' => ''
                        )
                    )
                )
            ),
            'activated' => 0
        );

        update_blog_option($blogId, 'searchwp_settings', $defaultSearchWpOptions);
    }

    /**
     * Adds site list rewrite rules
     * @return void
     */
    public function sitesListPageUrlRewrite()
    {
        add_rewrite_rule('^sites', 'index.php?site_list=all', 'top');
        add_rewrite_rule('^sites/?([a-zA-Z0-9_-]+)?', 'index.php?site_list=$matches[1]', 'top');
        add_rewrite_tag('%site_list%', '([^&]+)');

        flush_rewrite_rules();
    }

    /**
     * Get the site list template
     * @param  string $template Original template
     * @return string           Template to use
     */
    public function sitesListPageTemplate($template)
    {
        global $wp_query;

        if (!isset($wp_query->query['site_list'])) {
            return $template;
        }

        $template = \Municipio\Helper\Template::locateTemplate('sites-list');
        return $template;
    }
}

if (defined('MULTISITE') && MULTISITE) {
    new \IntranetDefaultSettings\IntranetDefaultSettings();
}
