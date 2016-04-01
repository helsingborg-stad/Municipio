<?php

namespace Municipio\Theme;

class Sidebars
{
    public function __construct()
    {
        add_action('widgets_init', array($this, 'register'));
        add_filter('Modularity/Module/Classes', array($this, 'moduleClasses'), 10, 3);
    }

    /**
     * Modify module classes in different areas
     * @param  array $classes      Default classes
     * @param  string $moduleType  Module type
     * @param  array $sidebarArgs  Sidebar arguments
     * @return array               Modified list of classes
     */
    public function moduleClasses($classes, $moduleType, $sidebarArgs)
    {
        // Box panel in content area and content area bottom
        if (in_array($sidebarArgs['id'], array('content-area', 'content-area-bottom')) && in_array('box-panel', $classes) && !is_front_page()) {
            $classes[] = 'box-panel-secondary';
        }

        // Inline box panels
        if (is_numeric($sidebarArgs['id']) && in_array('box-panel', $classes)) {
            $classes[] = 'box-panel-secondary';
        }

        // Sidebar boxes (should be filled)
        if (in_array($sidebarArgs['id'], array('left-sidebar-bottom', 'left-sidebar', 'right-sidebar')) && in_array('box-panel', $classes)) {
            unset($classes[array_search('box-panel', $classes)]);
            $classes[] = 'box-filled';
        }

        return $classes;
    }

    public function register()
    {
        /**
         * Footer Area
         */
        register_sidebar(array(
            'id'            => 'footer-area',
            'name'          => __('Footer', 'municipio'),
            'description'   => __('The footer area', 'municipio'),
            'before_widget' => '<div class="grid-lg-4"><div class="%2$s">',
            'after_widget'  => '</div></div>',
            'before_title'  => '<h2 class="footer-title">',
            'after_title'   => '</h2>'
        ));

        /**
         * Slider Area
         */
        register_sidebar(array(
            'id'            => 'slider-area',
            'name'          => __('Hero', 'municipio'),
            'description'   => __('The hero area', 'municipio'),
            'before_widget' => '<div class="%2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3>',
            'after_title'   => '</h3>'
        ));

        /**
         * Content Area
         */
        register_sidebar(array(
            'id'            => 'content-area',
            'name'          => __('Content area', 'municipio'),
            'description'   => __('The area below the content', 'municipio'),
            'before_widget' => '<div class="grid-sm-12"><div class="%2$s">',
            'after_widget'  => '</div></div>'
        ));

        /**
         * Content Area Bottom
         */
        register_sidebar(array(
            'id'            => 'content-area-bottom',
            'name'          => __('Main container bottom', 'municipio'),
            'description'   => __('The area below the main container', 'municipio'),
            'before_widget' => '<div class="grid-sm-12 grid-md-6 grid-lg-6"><div class="%2$s">',
            'after_widget'  => '</div></div>'
        ));

        /**
         * Left Sidebar
         */
        register_sidebar(array(
            'id'            => 'left-sidebar',
            'name'          => __('Left sidebar', 'municipio'),
            'description'   => __('The left sidebar area', 'municipio'),
            'before_widget' => '<div class="grid-lg-12"><div class="%2$s">',
            'after_widget'  => '</div></div>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>'
        ));

        /**
         * Left Sidebar Bottom
         */
        register_sidebar(array(
            'id'            => 'left-sidebar-bottom',
            'name'          => __('Left sidebar bottom', 'municipio'),
            'description'   => __('The area below the left sidebar content', 'municipio'),
            'before_widget' => '<div class="grid-lg-12"><div class="%2$s">',
            'after_widget'  => '</div></div>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>'
        ));

        /**
         * Right Sidebar
         */
        register_sidebar(array(
            'id'            => 'right-sidebar',
            'name'          => __('Right sidebar', 'municipio'),
            'description'   => __('The right sidebar area', 'municipio'),
            'before_widget' => '<div class="grid-lg-12"><div class="%2$s">',
            'after_widget'  => '</div></div>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>'
        ));
    }
}
