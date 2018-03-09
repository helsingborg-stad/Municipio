<?php

namespace Municipio;

class Feature
{
    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'featureEnabler'), 5);
    }

    /**
     * Filters to run (enables or disables features)
     * @return void
     */
    public function featureEnabler() // : void
    {
        //Enable customiser enabled views
        add_filter('Municipio/Controller/BaseController/Customizer', array($this, 'toogleFeatures'));

        //Enable customizer widgets
        add_filter('Municipio/Widget/Widgets/CustomizerWidgets', array($this, 'toogleFeatures'));

        //Enqueue bem styles
        add_filter('Municipio/Theme/Enqueue/Bem', array($this, 'toogleFeatures'));

        //Add bem-view path
        add_filter('Municipio/blade/view_paths', array($this, 'addBemViewPath'));

        //Enable template version 3 for modularity
        add_filter('Modularity/Module/TemplateVersion3', array($this, 'toogleFeatures'));
    }

    /**
     * Toggle feature
     * @param  bool  $boolean Previously filtered value
     * @return boolean
     */
    public function toogleFeatures($boolean) : bool
    {
        if (function_exists('get_field') && get_field('theme_mode', 'options') >= 2) {
            return true;
        }
        return false;
    }

    /**
     * Append new view paths for BEM
     * @param  array $viewLocations PReviously entered values
     * @return array
     */
    public function addBemViewPath($viewLocations) : array
    {
        if (function_exists('get_field') && get_field('theme_mode', 'options') >= 2) {
            array_unshift($viewLocations, get_stylesheet_directory() . '/bem-views', get_template_directory() . '/bem-views');
        }
        return $viewLocations;
    }
}
