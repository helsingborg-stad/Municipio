<?php

namespace Intranet\Theme;

class Enqueue
{
    public function __construct()
    {
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'style'));
        add_action('wp_enqueue_scripts', array($this, 'script'));

        // Enqueue admin
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueue'));
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function style()
    {
        wp_register_style('hbg-prime', 'http://hbgprime.dev/dist/css/hbg-prime.min.css', '', '1.0.0');
        wp_enqueue_style('hbg-prime');

        wp_enqueue_style('intranet', get_stylesheet_directory_uri(). '/assets/dist/css/app.min.css', '', filemtime(get_stylesheet_directory() . '/assets/dist/css/app.min.css'));
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function script()
    {
        wp_register_script('hbg-prime', 'http://hbgprime.dev/dist/js/hbg-prime.min.js', '', '1.0.0', true);
        wp_enqueue_script('hbg-prime');

        wp_enqueue_script('intranet', get_stylesheet_directory_uri(). '/assets/dist/js/app.min.js', '', filemtime(get_stylesheet_directory() . '/assets/dist/js/app.min.js'), true);
    }

    /**
     * Enqueue assets for admin
     * @return void
     */
    public function adminEnqueue()
    {
        wp_enqueue_style('intranet-admin', get_stylesheet_directory_uri(). '/assets/dist/css/admin.min.css', '', filemtime(get_stylesheet_directory() . '/assets/dist/css/admin.min.css'));

    }
}
