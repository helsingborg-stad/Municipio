<?php

namespace SsoAvailability;

class SsoAvailability
{
    public function __construct()
    {
        if (is_local_ip()) {
            if (!isset($_COOKIE['sso_available'])) {
                $this->check();
            }

            if (isset($_COOKIE['sso_available']) && $_COOKIE['sso_available'] != "false") {
                if ($_COOKIE['sso_available'] != $_SERVER['REMOTE_ADDR']) {
                    $this->check();
                }
            }

        } else {
            // Not in network, remove sso.
            if (isset($_COOKIE['sso_available'])) {
                unset($_COOKIE['sso_available']);
            }
            setcookie("sso_available", "", time()-3600);
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
                setCookie('".$_SERVER['REMOTE_ADDR']."');
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
                d.setTime(d.getTime() + (60 * 60 * 1000));

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

        if ($_COOKIE['sso_available'] === $_SERVER['REMOTE_ADDR']) {
            return true;
        }

        return false;
    }
}

new SsoAvailability();
