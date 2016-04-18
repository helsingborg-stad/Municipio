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
        if (in_array(array_shift(explode(".", $_SERVER['HTTP_HOST'])), array("bootstrap"))) {
            echo '<div class="hosting-enviroment hosting-yellow"><strong>' . __("Notice") . ": </strong>" . __("This it the test-environment. Your content will not be published.") . '</div>';
        }

        // Developer
        if (in_array(array_shift(explode(".", $_SERVER['HTTP_HOST'])), array("beta"))) {
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
}
