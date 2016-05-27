<?php

namespace Intranet\Module;

class UserLinks extends \Modularity\Module
{

    /**
     * Module args
     * @var array
     */
    public $args = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->args = array(
            'id' => 'intranet-user-links',
            'nameSingular' => __('User link', 'municipio-intranet'),
            'namePlural' => __('User links', 'municipio-intranet'),
            'description' => __('Shows a user\'s link list', 'municipio-intranet'),
            'supports' => array(),
            'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0NTcuMDMiIGhlaWdodD0iNDU3LjAzIiB2aWV3Qm94PSIwIDAgNDU3LjAzIDQ1Ny4wMyI+PHBhdGggZD0iTTQyMS41MTIgMjA3LjA3NGwtODUuNzk1IDg1Ljc2N2MtNDcuMzUyIDQ3LjM4LTEyNC4xNyA0Ny4zOC0xNzEuNTMgMC03LjQ2LTcuNDM4LTEzLjI5NS0xNS44Mi0xOC40Mi0yNC40NjRsMzkuODY0LTM5Ljg2YzEuODk2LTEuOTEyIDQuMjM2LTMuMDA3IDYuNDcyLTQuMjk3IDIuNzU2IDkuNDE1IDcuNTY3IDE4LjMzIDE0Ljk3MiAyNS43MzUgMjMuNjQ4IDIzLjY2NyA2Mi4xMjggMjMuNjM0IDg1Ljc2MiAwbDg1Ljc2OC04NS43NjVjMjMuNjY2LTIzLjY2NCAyMy42NjYtNjIuMTM1IDAtODUuNzgtMjMuNjM1LTIzLjY0Ny02Mi4xMDUtMjMuNjQ3LTg1Ljc2OCAwbC0zMC41IDMwLjUzYy0yNC43NS05LjYzNi01MS40MTQtMTIuMjI3LTc3LjM3Mi04LjQyM2w2NC45OS02NC45OWM0Ny4zOC00Ny4zNyAxMjQuMTc4LTQ3LjM3IDE3MS41NTggMCA0Ny4zNTcgNDcuMzcgNDcuMzU3IDEyNC4xOCAwIDE3MS41NDd6bS0yMjYuODA0IDE0MS4wM2wtMzAuNTIgMzAuNTMyYy0yMy42NDcgMjMuNjM0LTYyLjEzIDIzLjYzNC04NS43OCAwLTIzLjY0Ny0yMy42NjctMjMuNjQ3LTYyLjEzOCAwLTg1Ljc5NWw4NS43OC04NS43NjZjMjMuNjY0LTIzLjY2MiA2Mi4xMi0yMy42NjIgODUuNzY2IDAgNy4zODggNy4zOSAxMi4yMDQgMTYuMzAyIDE0Ljk4NiAyNS43MDYgMi4yNS0xLjMwNyA0LjU2LTIuMzcgNi40NTQtNC4yNjZsMzkuODYtMzkuODQ1Yy01LjA5LTguNjgtMTAuOTU3LTE3LjAzLTE4LjQyLTI0LjQ3OC00Ny4zNDgtNDcuMzctMTI0LjE3Mi00Ny4zNy0xNzEuNTQzIDBMMzUuNTI3IDI0OS45NmMtNDcuMzY2IDQ3LjM4NS00Ny4zNjYgMTI0LjE3MiAwIDE3MS41NTMgNDcuMzcgNDcuMzU2IDEyNC4xNzcgNDcuMzU2IDE3MS41NDcgMGw2NS4wMDgtNjUuMDAzYy0yNS45NyAzLjgyNi01Mi42NDMgMS4yMTMtNzcuMzcyLTguNDA2eiIvPjwvc3ZnPg=='
        );

        // This will register the module
        $this->register(
            $this->args['id'],
            $this->args['nameSingular'],
            $this->args['namePlural'],
            $this->args['description'],
            $this->args['supports'],
            $this->args['icon']
        );

        // Enqueue action
        //add_action('Modularity/Module/' . $this->moduleSlug . '/enqueue', array($this, 'enqueueAssets'));

        // Add our template folder as search path for templates
        add_filter('Modularity/Module/TemplatePath', function ($paths) {
            $paths[] = INTRANET_PATH . 'templates/';
            return $paths;
        });
    }

    /**
     * Enqueue your scripts and/or styles with wp_enqueue_script / wp_enqueue_style
     * @return
     */
    public function enqueueAssets()
    {

    }
}
