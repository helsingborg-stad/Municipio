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
            'id' => 'intranet-u-links',
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

        add_action('wp_ajax_add_user_link', array($this, 'addLink'));
        add_action('wp_ajax_remove_user_link', array($this, 'removeLink'));

        add_filter('Modularity/Display/' . $this->moduleSlug . '/Markup', array($this, 'restrictAccess'), 10, 2);
    }

    public function restrictAccess($markup, $module)
    {
        if (!is_user_logged_in()) {
            return '';
        }

        return $markup;
    }

    public static function getLinks()
    {
        if (!is_user_logged_in()) {
            return;
        }

        $userId = get_current_user_id();
        $links = (array)get_user_meta($userId, 'user_links', true);
        $links = array_filter($links);

        return $links;
    }

    public function removeLink()
    {
        // Do not contiue if user is not logged in
        if (!is_user_logged_in()) {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                echo 'Nope: 1';
                wp_die();
            }
            return;
        }

        // Stop if any field is not set
        if (!isset($_POST['url']) || empty($_POST['url'])) {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                echo 'Nope: 2';
                wp_die();
            }
            return;
        }

        $userId = get_current_user_id();
        $links = get_user_meta($userId, 'user_links', true);

        $foundKey = null;
        foreach ($links as $key => $link) {
            if ($link['url'] === sanitize_text_field($_POST['url'])) {
                $foundKey = $key;
            }
        }

        if ($foundKey !== false) {
            unset($links[$foundKey]);
            $links = array_values($links);
        }

        update_user_meta($userId, 'user_links', $links);

        if (defined('DOING_AJAX') && DOING_AJAX) {
            echo json_encode($links);
            wp_die();
        }
        return $links;
    }

    /**
     * Add a link to user's meta
     */
    public function addLink()
    {
        // Do not contiue if user is not logged in
        if (!is_user_logged_in()) {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                echo 'Nope: 1';
                wp_die();
            }
            return;
        }

        // Stop if any field is not set
        if (!isset($_POST['title']) || empty($_POST['title']) || !isset($_POST['url']) || empty($_POST['url'])) {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                echo 'Nope: 2';
                wp_die();
            }
            return;
        }

        $userId = get_current_user_id();
        $links = get_user_meta($userId, 'user_links', true);

        if (empty($links)) {
            $links = array();
        }

        // Check if duplicate url
        $links = array_filter($links, function ($item) {
            return $item['url'] !== sanitize_text_field($_POST['url']);
        });

        $links[] = array(
            'title' => sanitize_text_field($_POST['title']),
            'url' => sanitize_text_field($_POST['url'])
        );

        update_user_meta($userId, 'user_links', $links);

        if (defined('DOING_AJAX') && DOING_AJAX) {
            echo json_encode($links);
            wp_die();
        }
        return $links;
    }
}
