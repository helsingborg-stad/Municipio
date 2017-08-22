<?php

namespace Municipio\Theme;

class Sidebars
{
    public function __construct()
    {
        add_action('widgets_init', array($this, 'register'));
        add_filter('Modularity/Module/Classes', array($this, 'moduleClasses'), 10, 3);
        add_filter('Modularity/Module/WpWidget/before', array($this, 'wpWidgetBefore'), 10, 3);
        add_filter('Modularity/Editor/SidebarCompability', array($this, 'moduleSidebarCompability'), 10, 2);
    }

    public function wpWidgetBefore($before, $sidebarArgs, $module)
    {
        if (get_field('mod_standard_widget_type', $module->ID) == 'WP_Widget_Search') {
            return '<div>';
        }

        // Box panel in content area and content area bottom
        if (in_array($sidebarArgs['id'], array('content-area', 'content-area-bottom')) && !is_front_page()) {
            $before = '<div class="box box-panel box-panel-secondary">';
        }

        // Sidebar boxes (should be filled)
        if (in_array($sidebarArgs['id'], array('left-sidebar-bottom', 'left-sidebar', 'right-sidebar'))) {
            $before = '<div class="box box-filled">';
        }

        return $before;
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

        // Sidebar box-panel (should be filled)
        if (in_array($sidebarArgs['id'], array('left-sidebar-bottom', 'left-sidebar', 'right-sidebar')) && in_array('box-panel', $classes)) {
            unset($classes[array_search('box-panel', $classes)]);
            $classes[] = 'box-filled';
        }

        // Sidebar box-index (should be filled)
        if (in_array($sidebarArgs['id'], array('left-sidebar-bottom', 'left-sidebar', 'right-sidebar')) && in_array('box-index', $classes)) {
            unset($classes[array_search('box-index', $classes)]);
            $classes[] = 'box-filled';
        }

        // Sidebar box-news-horizontal (should be only box-news in sidebar)
        if (in_array($sidebarArgs['id'], array('left-sidebar-bottom', 'left-sidebar', 'right-sidebar')) && in_array('box-news-horizontal', $classes)) {
            unset($classes[array_search('box-news-horizontal', $classes)]);
        }

        return $classes;
    }

    public function register()
    {
        /**
         * Footer Area
         */
        $footerBeforeWidgetClass = get_field('footer_layout', 'option') == 'compressed' ? 'grid-lg-6' : 'grid-lg-4';
        register_sidebar(array(
            'id'            => 'footer-area',
            'name'          => __('Footer', 'municipio'),
            'description'   => __('The footer area', 'municipio'),
            'before_widget' => '<div class="' . $footerBeforeWidgetClass . '"><div class="%2$s">',
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
         * Content Area - Top
         */
        register_sidebar(array(
            'id'            => 'content-area-top',
            'name'          => __('Content area (above article)', 'municipio'),
            'description'   => __('The area above the content', 'municipio'),
            'before_widget' => '<div class="grid-sm-12"><div class="%2$s">',
            'after_widget'  => '</div></div>'
        ));

        /**
         * Content Area
         */
        register_sidebar(array(
            'id'            => 'content-area',
            'name'          => __('Content area (below article)', 'municipio'),
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
            'before_widget' => '<div class="grid-xs-12"><div class="%2$s">',
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
            'before_widget' => '<div class="grid-xs-12"><div class="%2$s">',
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
            'before_widget' => '<div class="grid-xs-12"><div class="%2$s">',
            'after_widget'  => '</div></div>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>'
        ));
    }

    /**
     * Appends compability support
     * @param  array $moduleSpecification Original module settings
     * @return array
     */

    public function moduleSidebarCompability($moduleSpecification, $modulePostType) : array
    {

        switch ($modulePostType) {
            case "mod-slider":
                $moduleSpecification['sidebar_compability'] = array("content-area", "content-area-top", "content-area-bottom", "slider-area");
                break;
            case "mod-table":
                $moduleSpecification['sidebar_compability'] = array("content-area", "content-area-top", "content-area-bottom");
                break;
        }

        return $moduleSpecification;
    }
}
