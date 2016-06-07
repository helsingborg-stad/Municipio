<?php

namespace Intranet\Module;

class UserSystems extends \Modularity\Module
{
    public function __construct()
    {
        $this->args = array(
            'id' => 'intranet-u-systems',
            'nameSingular' => __('User systems', 'municipio-intranet'),
            'namePlural' => __('User systems', 'municipio-intranet'),
            'description' => __('Shows a users\'s system link list', 'municipio-intranet'),
            'supports' => array(),
            'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgOTU2LjY5OSA5NTYuNjk5Ij48cGF0aCBkPSJNNzgyLjcgNDEzLjJoLS41Yy03LjctMTIxLjctMTA4LjktMjE4LTIzMi41LTIxOC0xMTQuMSAwLTIwOSA4Mi0yMjkuMSAxOTAuMi0yLjYtLjEtNS4zLS4yLTcuOS0uMi04NSAwLTE1Ni43IDU2LjMtMTgwLjEgMTMzLjYtMy42LS4zLTcuMy0uNS0xMS0uNUM1NC41IDUxOC4zIDAgNTcyLjcgMCA2MzkuOWMwIDY3LjIgNTQuNCAxMjEuNiAxMjEuNSAxMjEuNmg2NjEuMWM5Ni4yIDAgMTc0LjEtNzggMTc0LjEtMTc0LjEwMiAwLTk2LjEtNzcuOC0xNzQuMi0xNzQtMTc0LjJ6IiBmaWxsPSIjRkZGIi8+PC9zdmc+'
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

        // Add our template folder as search path for templates
        add_filter('Modularity/Module/TemplatePath', function ($paths) {
            $paths[] = INTRANET_PATH . 'templates/';
            return $paths;
        });

        add_action('init', array($this, 'saveUserSystems'));
    }

    public function saveUserSystems()
    {
        if (!isset($_REQUEST['select-systems']) || !wp_verify_nonce($_REQUEST['select-systems'], 'save')) {
            return;
        }

        update_user_meta(get_current_user_id(), 'user_systems', isset($_POST['system-selected']) ? $_POST['system-selected'] : null);

        wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }
}
