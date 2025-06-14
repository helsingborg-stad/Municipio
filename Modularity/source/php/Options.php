<?php

namespace Modularity;

abstract class Options
{
    /**
     * Will be set to menu_slug in the register function
     * @var string
     */
    protected $slug = null;

    /**
     * The hook name to use with the WP Load action (load-{$screenHook})
     * @var string
     */
    protected $screenHook = null;

    /**
     * Registers an options page
     * @param  string      $pageTitle  Page title
     * @param  string      $menuTitle  Menu title
     * @param  string      $capability Capability needed
     * @param  string      $menuSlug   Menu slug
     * @param  string/array $function   Callback function for content
     * @param  string      $iconUrl    Menu icon
     * @param  integer     $position   Menu position
     * @return void
     */
    public function register($pageTitle, $menuTitle, $capability, $menuSlug, $iconUrl = null, $position = null, $parent = 'modularity')
    {
        add_action('admin_menu', function () use ($pageTitle, $menuTitle, $capability, $menuSlug, $iconUrl, $position, $parent) {
            // Add the menu page
            $this->screenHook = add_submenu_page(
                $parent,
                $pageTitle,
                $menuTitle,
                $capability,
                $menuSlug,
                array($this, 'optionPageTemplate')
            );

            // Set the slug
            $this->slug = $menuSlug;

            // Setup meta box support
            add_action('load-' . $this->screenHook, array($this, 'save'), 1);
            add_action('load-' . $this->screenHook, array($this, 'setupMetaBoxSupport'), 2);

            // Hook to add the metaboxes
            add_action('add_meta_boxes_' . $this->screenHook, array($this, 'addMetaBoxes'));
        });
    }

    /**
     * This function should be used to add the desiered meta boxes
     * Override it in your options class
     */
    public function addMetaBoxes()
    {
        return true;
    }

    /**
     * Validates post save
     * @return boolean
     */
    public function isValidPostSave()
    {
        if (!isset($_POST['modularity-action']) || $_POST['modularity-action'] !== 'modularity-options') {
            return false;
        }

        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'modularity-options')) {
            return false;
        }

        return true;
    }

    /**
     * Saves the options
     * @return void
     */
    public function save()
    {
        if (!$this->isValidPostSave()) {
            return;
        }

        // Get the options
        global $modularityOptions;
        $modularityOptions = (isset($_POST['modularity-options'])) ? $_POST['modularity-options'] : array();

        // Update the options
        update_option($this->slug, $modularityOptions);

        do_action('Modularity/Options/Save');

        // All done, send notice
        $this->notice(__('Options saved successfully', 'modularity'), ['updated']);
    }

    /**
     * Sends a notice to the user
     * @param  string $message The noticce message
     * @param  array  $class   List of DOM classes to use on the notice
     * @return void
     */
    protected function notice($message, $class = array())
    {
        add_action('admin_notices', function () use ($message, $class) {
            $class = implode(' ', $class);
            echo '<div class="notice ' . $class . '"><p>' . $message . '</p></div>';
        });
    }

    /**
     * Add metabox support to the options page
     * @return void
     */
    public function setupMetaBoxSupport()
    {
        do_action('add_meta_boxes_' . $this->screenHook, null);
        do_action('add_meta_boxes', $this->screenHook, null);

        add_screen_option('layout_columns', array('max' => 2, 'default' => 2));
    }

    /**
     * Renders the option page markup template
     * @return void
     */
    public function optionPageTemplate()
    {
        wp_enqueue_script('postbox');

        global $modularityOptions;
        $modularityOptions = get_option($this->slug);

        // Load template file
        require_once MODULARITY_TEMPLATE_PATH . 'options/modularity-options.php';
    }

    /**
     * Get input field name (modularitu-options[$name])
     * The $name will be the options key later on
     *
     * @param  string $name      Desired field name
     * @param  boolean $multiple Should the field accept multiple values (array)
     * @return string            The full field name
     */
    protected function getFieldName($name, $multiple = false)
    {
        if (!$multiple) {
            return 'modularity-options[' . $name . ']';
        }

        return 'modularity-options[' . $name . '][]';
    }
}
