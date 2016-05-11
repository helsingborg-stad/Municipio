<?php

/*
Plugin Name:    Composer WordPress Network URL
Description:    Fixes issues with network-admin url when using wp-composer setup.
Version:        1.0
Author:         Sebastian Thulin
*/

namespace ComposerNetworkAdmin;

class ComposerNetworkAdmin
{
    public function __construct()
    {
        if (is_multisite()) {
            add_filter('network_admin_url', array($this, 'sanitizeNetworkAdminUrl'), 50, 2);
        }

        add_filter('admin_url', array($this, 'sanitizeAdminUrl'), 50, 3);
        //add_filter('login_url', array($this, 'sanitizeLoginUrl'), 10, 2);
    }

    public function sanitizeLoginUrl($login_url, $redirect)
    {
        return home_url('/wp/wp-login.php?redirect_to=' . $redirect);
    }

    public function sanitizeAdminUrl($url, $path, $blog_id)
    {
        if (strpos($url, '/wp/wp-admin') === false && !strpos($url, '/network')) {
            return str_replace('/wp-admin/', '/wp/wp-admin/', $url);
        } else {
            return $url;
        }
    }

    public function sanitizeNetworkAdminUrl($url, $path)
    {
        if (strpos($url, '/wp/wp-admin/network') === false && strpos($url, '/network')) {
            return str_replace('/wp-admin/', '/wp/wp-admin/', $url);
        } else {
            return $url;
        }
    }
}

new \ComposerNetworkAdmin\ComposerNetworkAdmin();
