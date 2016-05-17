<?php

namespace Intranet\Modules;

class Login extends \Modularity\Module
{
    /**
     * Module args
     * @var array
     */
    public $args = array(
        'id' => 'login',
        'nameSingular' => 'Login',
        'namePlural' => 'Login',
        'description' => 'Displays a login form (wp-login)',
        'supports' => array(),
        'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0ODYuNzMzIiBoZWlnaHQ9IjQ4Ni43MzMiIHZpZXdCb3g9IjAgMCA0ODYuNzMzIDQ4Ni43MzMiPjxwYXRoIGQ9Ik00MDMuODggMTk2LjU2M2gtOS40ODR2LTQ0LjM4OGMwLTgyLjEtNjUuMTUtMTUwLjY4LTE0Ni41ODItMTUyLjE0NS0yLjIyNS0uMDQtNi42Ny0uMDQtOC44OTUgMEMxNTcuNDg1IDEuNDk0IDkyLjMzNSA3MC4wNzYgOTIuMzM1IDE1Mi4xNzV2NDQuMzg4SDgyLjg1Yy0xNC42MTUgMC0yNi41MzcgMTUuMDgyLTI2LjUzNyAzMy43MXYyMjIuNjNjMCAxOC42MDcgMTEuOTIyIDMzLjgzIDI2LjU0IDMzLjgzSDQwMy44OGMxNC42MTYgMCAyNi41NC0xNS4yMjMgMjYuNTQtMzMuODN2LTIyMi42M2MwLTE4LjYyNy0xMS45MjMtMzMuNzEtMjYuNTQtMzMuNzF6bS0xMzAuNDM4IDE0NC44djY3LjI3YzAgNy43MDMtNi40NSAxNC4yMjItMTQuMTU4IDE0LjIyMkgyMjcuNDVjLTcuNzEgMC0xNC4xNi02LjUyLTE0LjE2LTE0LjIyMnYtNjcuMjdjLTcuNDc2LTcuMzYtMTEuODMtMTcuNTM4LTExLjgzLTI4Ljc5NiAwLTIxLjMzNCAxNi40OTItMzkuNjY2IDM3LjQ2LTQwLjUxMyAyLjIyMi0uMDkgNi42NzMtLjA5IDguODk1IDAgMjAuOTY4Ljg0NyAzNy40NiAxOS4xOCAzNy40NiA0MC41MTMtLjAwMyAxMS4yNTgtNC4zNTYgMjEuNDM1LTExLjgzMyAyOC43OTV6bTU4LjQ0NC0xNDQuOGgtMTc3LjA0di00NC4zODhjMC00OC45MDUgMzkuNzQ0LTg5LjM0MiA4OC41Mi04OS4zNDIgNDguNzc0IDAgODguNTIgNDAuNDM3IDg4LjUyIDg5LjM0MnY0NC4zODh6Ii8+PC9zdmc+'
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        // This will register the module
        $this->register(
            $this->args['id'],
            $this->args['nameSingular'],
            $this->args['namePlural'],
            $this->args['description'],
            $this->args['supports'],
            $this->args['icon']
        );

        // Add our template folder as search path for templates
        add_filter('Modularity/Module/TemplatePath', function ($paths) {
            $paths[] = INTRANET_PATH . 'templates/';
            return $paths;
        });
    }
}

new \Intranet\Modules\Login();
