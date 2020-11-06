<?php

namespace Municipio\Admin\TinyMce;

abstract class PluginClass
{
    /**
     * Name of the TinyMCE plugin compatible JS file (including extension)
     * @var string
     */
    protected $jsFile;

    /**
     * Name of the TinyMCE plugin slug that is defined in JS file
     * @var string
     */
    protected $pluginSlug;

    /**
     * Holds localize data
     * @var array
     */
    protected $data = array();

    /**
     * Filter name used to place button in editor
     * @var string
     */
    protected $buttonFilter = 'mce_buttons';

    /**
     * Used to modify button filter, if buttonRow = 2 buttonFilter will be mce_buttons_2 (places button on second row)
     * @var string / int
     */
    protected $buttonRow;

    public function __construct()
    {
        add_action('admin_init', array($this, 'setupTinyMcePlugin'));
    }

    /**
    * Check if the current user can edit Posts or Pages, and is using the Visual Editor
    * If so, add some filters so we can register our plugin
    */
    public function setupTinymcePlugin()
    {
        if (! current_user_can('edit_posts') && ! current_user_can('edit_pages')) {
            return;
        }

        // Check if the logged in WordPress User has the Visual Editor enabled
        // If not, don't register our TinyMCE plugin
        if (get_user_option('rich_editing') !== 'true') {
            return;
        }

        $this->init();

        if (!$this->pluginSlug || !$this->pluginSlug) {
            return;
        }

        //Change button row placement if buttonRow is defined
        if (isset($this->buttonRow) && is_numeric($this->buttonRow) && $this->buttonRow > 1) {
            $this->buttonFilter .= '_' . $this->buttonRow;
        }

        //LocalizeData (if any)
        if (is_array($this->data) && !empty($this->data)) {
            add_action('admin_head', array($this, 'localizeScript'));
        }

        add_filter('mce_external_plugins', array($this, 'addTinyMcePlugin'));
        add_filter($this->buttonFilter, array($this, 'addTinymceToolbarButton' ));
    }

    /**
    * Adds a TinyMCE plugin compatible JS file to the TinyMCE / Visual Editor instance
    *
    * @param array $plugin_array Array of registered TinyMCE Plugins
    * @return array Modified array of registered TinyMCE Plugins
    */
    public function addTinyMcePlugin($plugins)
    {
        $plugins[$this->pluginSlug] = get_template_directory_uri() . '/assets/dist/' .
            \Municipio\Helper\CacheBust::getFilename('js/mce.js');

        return $plugins;
    }

    /**
    * Adds a button to the TinyMCE / Visual Editor which the user can click
    * to insert a link with a custom CSS class.
    *
    * @param array $buttons Array of registered TinyMCE Buttons
    * @return array Modified array of registered TinyMCE Buttons
    */
    public function addTinymceToolbarButton($buttons)
    {
        array_push($buttons, '', $this->pluginSlug);
        return $buttons;
    }

    public function localizeScript()
    {
        $output = '<script type="text/javascript" atr="lol">';
        $output .= 'var ' . $this->pluginSlug . ' = {';

        foreach ($this->data as $key => $value) {
            $output .= "'" . $key . "' : '" . $value . "',";
        }

        $output .= '} </script>';

        echo $output;
    }
}
