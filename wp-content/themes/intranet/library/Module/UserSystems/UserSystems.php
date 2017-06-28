<?php

namespace Intranet\Module;

class UserSystems extends \Modularity\Module
{
    public $slug = 'intranet-u-systems';
    public $icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgOTU2LjY5OSA5NTYuNjk5Ij48cGF0aCBkPSJNNzgyLjcgNDEzLjJoLS41Yy03LjctMTIxLjctMTA4LjktMjE4LTIzMi41LTIxOC0xMTQuMSAwLTIwOSA4Mi0yMjkuMSAxOTAuMi0yLjYtLjEtNS4zLS4yLTcuOS0uMi04NSAwLTE1Ni43IDU2LjMtMTgwLjEgMTMzLjYtMy42LS4zLTcuMy0uNS0xMS0uNUM1NC41IDUxOC4zIDAgNTcyLjcgMCA2MzkuOWMwIDY3LjIgNTQuNCAxMjEuNiAxMjEuNSAxMjEuNmg2NjEuMWM5Ni4yIDAgMTc0LjEtNzggMTc0LjEtMTc0LjEwMiAwLTk2LjEtNzcuOC0xNzQuMi0xNzQtMTc0LjJ6IiBmaWxsPSIjRkZGIi8+PC9zdmc+';
    public $supports = array();

    public $templateDir = INTRANET_TEMPLATE_PATH . 'module';

    public function init()
    {
        $this->nameSingular = __('User systems', 'municipio-intranet');
        $this->namePlural = __('User systems', 'municipio-intranet');
        $this->description = __('Shows a users\'s system link list', 'municipio-intranet');
        $this->cacheTtl = 0;

        add_action('template_redirect', array($this, 'saveUserSystems'));
        add_filter('Modularity/Display/' . $this->moduleSlug . '/Markup', array($this, 'restrictAccess'), 10, 2);
    }

    public function data() : array
    {
        $data = array();
        $data['selectedSystems'] = \Intranet\User\Systems::getAvailabelSystems('user', array('user_only_selected'));
        $data['availableSystems'] = \Intranet\User\Systems::getAvailabelSystems('user', array('user'));

        return $data;
    }

    public function saveUserSystems()
    {
        if (!isset($_REQUEST['select-systems']) || !wp_verify_nonce($_REQUEST['select-systems'], 'save')) {
            return;
        }

        //Determine operator char
        if (strpos($_SERVER['HTTP_REFERER'], "?") !== false) {
            $operator = "&";
        } else {
            $operator = "?";
        }

        //Update and redirect
        if (update_user_meta(get_current_user_id(), 'user_systems', isset($_POST['system-selected']) ? $_POST['system-selected'] : null)) {
            wp_redirect($_SERVER['HTTP_REFERER'] . $operator . "save-system=saved");
        } else {
            wp_redirect($_SERVER['HTTP_REFERER'] . $operator . "save-system=error");
        }

        exit;
    }

    /**
     *
     * Restrict access for the module (only logged in users)
     * @param  string $markup Markup
     * @param  object $module Module post object
     * @return string         Markup
     */
    public function restrictAccess($markup, $module)
    {
        if (!is_user_logged_in()) {
            return '';
        }

        return $markup;
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization (if you must, use __construct with care, this will probably break stuff!!)
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
