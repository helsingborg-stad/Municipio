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
        //Redirect correct url
        $this->redirectToCorrectUrl((isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        //Filter genereated url's
        add_filter('admin_url', function ($url, $path, $blog_id = null) {
            return $this->fixUrl($url);
        }, 9999, 3);
    }

    public function redirectToCorrectUrl($url)
    {
        if (preg_match("~/wp/wp-admin/post.php/wp/wp-admin/~i", $url)) {
            header("Location: " . $this->fixUrl($url));
            exit;
        }
    }

    public function fixUrl($url)
    {
        return str_replace("/wp/wp-admin/post.php/wp/wp-admin/", "/wp/wp-admin/", $url);
    }
}
new \AdminUrlBugFix\AdminUrlBugFix();
