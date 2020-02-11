<?php

namespace BladeComponentLibrary;

use HelsingborgStad\Blade\Blade as Blade;

class Init
{

    public function __construct()
    {
        /*Register::setCachePath(
            WP_CONTENT_DIR . '/uploads/cache/blade-cache/utility'
        );*/ 

        Register::addViewPath(
            dirname(__FILE__) . "View" . DIRECTORY_SEPARATOR; 
        ); 

        Register::addControllerPath(
            dirname(__FILE__) . "Controller" . DIRECTORY_SEPARATOR; 
        );

        Register::add(
            'button',
            [
                'isPrimary' => true,
                'isDisabled' => false, 
                'isOutlined' => true,

                'label' => "Button text",
                'href' => "https://google.se",

                'target' => "_self"
            ],
            'button.blade.php' // You can leave this out, it will automatically be generated from slug. 
        );

        Register::add(
            'date',
            [
                'hasTime' => false,
                'hasDate' => true, 
                'isHumanReadable' => true
            ],
            'date-time.blade.php'
        );
    }
    
}