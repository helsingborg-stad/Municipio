<?php

namespace IntranetDefaultSettings;

class IntranetDefaultSettings
{
    public function __construct()
    {
        add_action('wpmu_new_blog', array($this, 'setHeaderLayout'));
        add_action('admin_init', array($this, 'createSiteListPage'));
    }

    public function setHeaderLayout($blogId)
    {
        // Set the intranet header as default header style
        update_blog_option($blogId, 'options_header_layout', 'intranet');

        // Show date published and date modified per default
        update_blog_option($blogId, 'options_show_date_updated', array('post', 'page', 'intranet-news'));
        update_blog_option($blogId, 'options_show_date_published', array('post', 'page', 'intranet-news'));
    }

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

