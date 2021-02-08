<?php

namespace Municipio\Admin;

class ExternalDeptendents
{

    public function __construct()
    {
      //Register requirements
      add_action('tgmpa_register', array($this, 'registerExternalDepends'));

    }

    /**
     * Registers external dependencies
     * @return void
     */

    public function registerExternalDepends()
    {

      //Example plugin specification
      /*
      'name'               => 'TGM Example Plugin', // The plugin name.
			'slug'               => 'tgm-example-plugin', // The plugin slug (typically the folder name).
			'source'             => get_stylesheet_directory() . '/lib/plugins/tgm-example-plugin.zip', // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
      */

      //Declare plugins array
      $plugins = []; 

      //Advanced custom fields
      $plugins[] = [
        'name'              => 'Advanced custom fields PRO',
        'slug'              => 'advanced-custom-fields-pro',
        'required'          => true,
        'force_activation'  => true
      ]; 

      //Component library
      $plugins[] = [
        'name'              => 'Component library',
        'slug'              => 'component-library',
        'required'          => true,
        'force_activation'  => true,
        'source'            => 'https://github.com/helsingborg-stad/component-library/archive/master.zip' 
      ]; 

      //Modularity
      $plugins[] = [
        'name'              => 'Modularity',
        'slug'              => 'modularity',
        'required'          => false,
        'force_activation'  => false,
        'source'            => 'https://github.com/helsingborg-stad/Modularity/archive/3.0/develop.zip'
      ]; 

      tgmpa($plugins,[
          'id'           => 'tgmpa-municipio',       // Unique ID for hashing notices for multiple instances of TGMPA.
          'default_path' => '',                      // Default absolute path to bundled plugins.
          'menu'         => 'tgmpa-install-plugins', // Menu slug.
          'parent_slug'  => 'themes.php',            // Parent menu slug.
          'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
          'has_notices'  => true,                    // Show admin notices or not.
          'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
          'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
          'is_automatic' => false,                   // Automatically activate plugins after installation or not.
          'message'      => 'Important notice: This platform is intended to be used with composer, please install all requirements with composer.',
      ]);
    }
}
