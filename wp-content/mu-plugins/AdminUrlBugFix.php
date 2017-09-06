<?php
/*
Plugin Name: Admin URL bugfix
Description: This is a temporary fix for faulty admin URL's. Somehow we cannot localize the cause of the faulty links. However; We can identify them.
Version:     1.0
Author:      Sebastian Thulin
*/

namespace AdminUrlBugFix;

class AdminUrlBugFix
{
    public function __construct()
    {
        $this->redirectToCorrectUrl((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI]);
    }

    public function redirectToCorrectUrl($url)
    {
        if (preg_match("~/wp/wp-admin/post.php/wp/wp-admin/~i", $url)) {
            header("Location: " . str_replace("/wp/wp-admin/post.php/wp/wp-admin/", "/wp/wp-admin/", $url));
            exit;
        }
    }
}
new \AdminUrlBugFix\AdminUrlBugFix();
