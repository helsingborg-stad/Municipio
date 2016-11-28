<?php

namespace SsoAvailability;

class SsoAvailability
{
    public function __construct()
    {
        if (!$this->isLoggedIn()) {
            $this->check();
        }
    }

    public function check()
    {
        $urlPart = $_SERVER['REQUEST_URI'];
        $urlPart = explode('?', $urlPart)[0];

        if (isset($_GET['trigger_sso'])) {
            unset($_GET['trigger_sso']);
        }

        $url = "//$_SERVER[HTTP_HOST]$urlPart";
        $querystring = http_build_query($_GET);
        if (!empty($querystring)) {
            $url .= '?' . $querystring;
        }

        echo "
        <script type=\"text/javascript\">
            var imageUrl = 'https://fs01.hbgadm.hbgstad.se/adfs/portal/illustration/illustration.png?id=183128A3C941EDE3D9199FA37D6AA90E0A7DFE101B37D10B4FEDA0CF35E11AFD';
            var image = document.createElement('img');

            image.addEventListener('load', function () {
                setCookie(true);
                location.href = '" . $url . "';
            });

            image.addEventListener('error', function () {
                setCookie(false);
                location.href = '" . $url . "';
            });

            image.src = imageUrl;

            function setCookie(value) {
                var d = new Date();
                var name = 'sso_available';
                var daysValid = 2;

                d.setTime(d.getTime() + (daysValid * 24 * 60 * 60 * 1000));

                var expires = 'expires=' + d.toUTCString();
                document.cookie = name + '=' + value.toString() + '; ' + expires + '; domain=" . COOKIE_DOMAIN . "; path=/;';

                return true;
            }
        </script>
        ";
        exit();
    }

    public static function isSsoAvailable()
    {
        if (!isset($_COOKIE['sso_available'])) {
            return false;
        }

        if ($_COOKIE['sso_available'] == 'true') {
            return true;
        }

        return false;
    }

    public function isLoggedIn()
    {
        if (is_array($_COOKIE) && !empty($_COOKIE)) {
            foreach ($_COOKIE as $key => $val) {
                if (preg_match("/wordpress_logged_in/i", $key)) {
                    if (!empty($val)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}

if (!isset($_COOKIE['sso_available']) && (is_local_ip() || (isset($_GET['trigger_sso']) && $_GET['trigger_sso'] == 'true'))) {
    new SsoAvailability();
}
