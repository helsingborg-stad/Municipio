<?php

/*
Plugin Name:    Before Live Temporary Redirect
Description:    Redirects to beta site while not live.
Version:        1.0
Author:         Sebastian Thulin
*/

namespace BFLTR;

class BeforeLiveTemporaryRedirect
{

    private $isAllowed = false;

    public function __construct()
    {
        if ($_SERVER['HTTP_HOST'] == 'intranat.helsingborg.se') {
            if (isset($_GET['letmein']) && !isset($_SESSION['letmein'])) {
                $this->setCookie();
            }

            if ($this->checkCookie() === false && !isset($_GET['letmein'])) {
                header('Location: https://beta.intranat.helsingborg.se');
                exit;
            }
        }
    }

    public function setCookie()
    {
        $_SESSION['letmein'] = 1;
    }

    public function checkCookie()
    {
        if (isset($_SESSION['letmein']) && $_SESSION['letmein'] == 1) {
            return true;
        }
        return false;
    }
}

new \BFLTR\BeforeLiveTemporaryRedirect();
