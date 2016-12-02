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
            if (is_admin() && (!defined('DOING_AJAX') || (defined('DOING_AJAX') && !DOING_AJAX))) {
                wp_die('Vi har flyttat intanätet hit: <a href="https://intranat.helsingborg.se/">intranat.helsingborg.se</a>.');
            }
        }

        if (in_array($_SERVER['HTTP_HOST'], array('intranat.helsingborg.se'))) {
            if (is_admin() && (!defined('DOING_AJAX') || (defined('DOING_AJAX') && !DOING_AJAX))) {
                wp_die('På grund av byte av server kan du just nu inte redigera information på intranätet. Om du har frågor kring detta, vänligen kontakta servicedesk.');
            }
        }
    }
}

new \BFLTA\BeforeLiveTemporaryAdmin();
