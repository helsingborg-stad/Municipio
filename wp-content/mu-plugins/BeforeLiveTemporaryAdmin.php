<?php

/*
Plugin Name:    Before Live Temporary Redirect
Description:    Redirects to beta site while not live.
Version:        1.0
Author:         Sebastian Thulin
*/

namespace BFLTA;

class BeforeLiveTemporaryAdmin
{

    private $isAllowed = false;

    public function __construct()
    {
        if (in_array($_SERVER['HTTP_HOST'], array('beta.intranat.helsingborg.se'))) {
            if (is_admin() && (! defined('DOING_AJAX') || (defined('DOING_AJAX') && !DOING_AJAX))) {
                wp_die('Vi har flyttat intanätet hit: <a href="https://intranat.helsingborg.se/">intranat.helsingborg.se</a>.');
            }
        }
    }
}

new \BFLTA\BeforeLiveTemporaryAdmin();
