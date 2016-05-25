<?php

namespace IntranetDefaultSettings;

class IntranetDefaultSettings
{
    public function __construct()
    {
        // Blogs
        add_action('wpmu_new_blog', array($this, 'createFrontPage'));
        add_action('wpmu_new_blog', array($this, 'setDefaultSiteSettings'));

        // Portal
        add_action('admin_init', array($this, 'createSiteListPage'));
    }

    /**
     * Sets the default site settings
     * Add new settings with $key => $value structure
     * @param [type] $blogId [description]
     */
    public function setDefaultSiteSettings($blogId)
    {
        $defaultBlogOptions = array(

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
             'options_footer_signature_show'     => 1

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
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => __('Front page')
        ));

        update_blog_option($blogId, 'show_on_front', 'page');
        update_blog_option($blogId, 'page_on_front', $frontPageId);

        restore_current_blog();
    }

    /**
     * Creates a page that holds a list of all sites in the network
     * @return void
     */
    public function createSiteListPage()
    {
        global $current_site;

        switch_to_blog($current_site->blog_id);

        $pageTitle = 'Network sites';
        $exists = get_posts(array(
            's' => $pageTitle,
            'post_status' => 'publish',
            'post_type' => 'page'
        ));

        if ($exists) {
            restore_current_blog();
            return;
        }

        require_once get_template_directory() . '/library/Helper/Template.php';
        $inserted = wp_insert_post(array(
            'post_type' => 'page',
            'post_title' => $pageTitle,
            'post_status' => 'publish',
            'meta_input' => array(
                '_wp_page_template' => \Municipio\Helper\Template::locateTemplate('network-sites-list.blade.php')
            )
        ));

        restore_current_blog();
    }
}

if (defined('MULTISITE') && MULTISITE) {
    new \IntranetDefaultSettings\IntranetDefaultSettings();
}
