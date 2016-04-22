<?php

namespace Municipio\Admin\UI;

class BackEnd
{
    public function __construct()
    {
        add_action('admin_footer', array($this, 'hostingEnviroment'));
    }

    public function hostingEnviroment()
    {
        // Editor testing zone
        if ($this->isLocal()) {
            echo '<div class="hosting-enviroment hosting-yellow"><strong>' . __("Notice") . ": </strong>" . __("You're on a local server.") . '</div>';
        }

        // Editor testing zone
        if ($this->isTest()) {
            echo '<div class="hosting-enviroment hosting-yellow"><strong>' . __("Notice") . ": </strong>" . __("This it the test-environment. Your content will not be published.") . '</div>';
        }

        // Developer
        if ($this->isBeta()) {
            echo '<div class="hosting-enviroment hosting-red"><strong>' . __("Notice") . ": </strong>" . __("This it the beta-environment. All functionality is not guaranteed.") . '</div>';
        }

        // Css
        echo '
            <style>
                .hosting-enviroment {
                    position: fixed;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    padding: 10px;
                    color: #fff;
                    z-index: 99;
                    text-align: center;
                }
                .hosting-enviroment.hosting-red {
                    background-color: #e3000f;
                }
                .hosting-enviroment.hosting-yellow {
                    background-color: gold;
                    color: #000;
                }
            </style>
        ';
    }

    public function isLocal()
    {
        return $_SERVER['SERVER_ADDR'] == '127.0.0.1' && !isset($_SERVER['HTTP_X_VARNISH']);
    }

    public function isTest()
    {
        return strpos($_SERVER['HTTP_HOST'], 'test.') > -1;
    }

    public function isBeta()
    {
        return strpos($_SERVER['HTTP_HOST'], 'beta.') > -1;
    }
}
