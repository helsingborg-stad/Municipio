<?php

namespace Municipio\Admin\UI;

class BackEnd
{
    public function __construct()
    {
        add_action('admin_footer', array($this, 'hostingEnviroment'));
        add_action('admin_title', array($this, 'prefixTitle'));
        add_action('wp_title', array($this, 'prefixTitle'));
    }

    public function prefixTitle($title)
    {
        if (!$this->isLocal() && !$this->isTest() && !$this->isBeta()) {
            return $title;
        }

        $prefix = null;

        if ($this->isLocal()) {
            $prefix = __('Local', 'municipio');
        }

        if ($this->isTest()) {
            $prefix = __('Test', 'municipio');
        }

        if ($this->isBeta()) {
            $prefix = __('Beta', 'municipio');
        }

        return '(' . $prefix . ') ' . $title;
    }

    public function hostingEnviroment()
    {
        // Editor testing zone
        if ($this->isLocal()) {
            echo '<div class="hosting-enviroment hosting-yellow"><strong>' . __('Notice', 'municipio') . ": </strong>" . __('You\'re on a local server.', 'municipio') . '</div>';
        }

        // Editor testing zone
        if ($this->isTest()) {
            echo '<div class="hosting-enviroment hosting-yellow"><strong>' . __('Notice', 'municipio') . ": </strong>" . __('This it the test-environment. Your content will not be published.', 'municipio') . '</div>';
        }

        // Developer
        if ($this->isBeta()) {
            echo '<div class="hosting-enviroment hosting-red"><strong>' . __('Notice', 'municipio') . ": </strong>" . __('This it the beta-environment. All functionality is not guaranteed.', 'municipio') . '</div>';
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
