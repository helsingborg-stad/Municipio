<?php

namespace Intranet\Theme;

class Walkthrough
{
    public function __construct()
    {
        $this->exitNotice();
    }

    public function exitNotice()
    {
        if (!isset($_GET['walkthrough'])) {
            return;
        }

        add_action('init', function () {
            $querystring = \Municipio\Helper\Url::queryStringExclude($_SERVER['QUERY_STRING'], array('walkthrough'));


            \Municipio\Helper\Notice::add(
                __('You are in <strong>Walkthrough mode</strong>. Click one of the flashing dots to get started.', 'municipio-intranet'),
                'info',
                'pricon pricon-compass',
                array(
                    array(
                        'url' => strlen($querystring) > 0 ? '?' . $querystring : strtok($_SERVER['REQUEST_URI'], '?'),
                        'text' => __('Exit', 'municipio-intranet'),
                        'class' => 'btn btn-danger btn-md'
                    )
                )
            );
        });
    }
}
