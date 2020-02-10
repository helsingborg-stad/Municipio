<?php

namespace Municipio\Theme;

class Sidebars
{
    public function __construct()
    {
        add_action('widgets_init', array($this, 'register'));
        add_filter('Modularity/Module/Classes', array($this, 'moduleClasses'), 10, 3);
        add_filter('Modularity/Module/WpWidget/before', array($this, 'wpWidgetBefore'), 10, 3);
        add_filter('Modularity/Editor/SidebarIncompability', array($this, 'moduleSidebarIncompability'), 10, 2);

        add_filter('Modularity/Module/Container/Sidebars', array($this, 'registerSidebarWithContainerSupport'));
        add_filter('Modularity/Module/Container/Modules', array($this, 'registerModulesWithContainerSupport'));
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
         * Full page top
         */
        register_sidebar(array(
            'id'            => 'top-sidebar',
            'name'          => __('Top sidebar (full-width)', 'municipio'),
            'description'   => __('Sidebar that sits below the hero, takes up 100% of the widht.', 'municipio'),
            'before_widget' => '<div class="%2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3>',
            'after_title'   => '</h3>'
        ));

        /**
         * Full page bottom
         */
        register_sidebar(array(
            'id'            => 'bottom-sidebar',
            'name'          => __('Bottom sidebar (full-width)', 'municipio'),
            'description'   => __('Sidebar that sits just before the footer, takes up 100% of the widht.', 'municipio'),
            'before_widget' => '<div class="%2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3>',
            'after_title'   => '</h3>'
        ));
    }

    /**
     * Appends compability support
     * @param  array $moduleSpecification Original module settings
     * @return array
     */

    public function moduleSidebarIncompability($moduleSpecification, $modulePostType) : array
    {

        switch ($modulePostType) {
            case "mod-section-featured":
            case "mod-section-full":
            case "mod-section-split":
                $moduleSpecification['sidebar_incompability'] = array("right-sidebar", "left-sidebar", "left-sidebar-bottom", "footer-area", "content-area-bottom", "content-area", "content-area-top", "footer-area");
                break;
            case "mod-slider":
            case "mod-video":
                $moduleSpecification['sidebar_incompability'] = array("right-sidebar", "left-sidebar", "left-sidebar-bottom", "footer-area");
                break;
            case "mod-table":
            case "mod-gallery":
            case "mod-guide":
            case "mod-alarms":
            case "mod-interactive-map":
                $moduleSpecification['sidebar_incompability'] = array("right-sidebar", "left-sidebar", "left-sidebar-bottom", "footer-area", "slider-area", "bottom-sidebar", "top-sidebar");
                break;
            case "mod-posts":
            case "mod-location":
            case "mod-social":
            case "mod-dictionary":
            case "mod-contacts":
            case "mod-fileslist":
            case "mod-g-calendar":
            case "mod-index":
            case "mod-inlaylist":
                $moduleSpecification['sidebar_incompability'] = array("footer-area", "slider-area", "bottom-sidebar", "top-sidebar");
                break;
            case "mod-rss":
            case "mod-script":
            case "mod-notice":
            case "mod-iframe":
            case "mod-event":
            case "mod-form":
            case "mod-location":
            case "mod-text":
                $moduleSpecification['sidebar_incompability'] = array("slider-area", "bottom-sidebar", "top-sidebar");
                break;
        }

        return $moduleSpecification;
    }

    /**
     * Add container grid to some modules placed in full-width widget areas
     * @return array
     */

    public function registerSidebarWithContainerSupport($sidebars)
    {
        $sidebars[] = "top-sidebar";
        $sidebars[] = "bottom-sidebar";
        return $sidebars;
    }

    /**
     * Add container grid to some modules placed in full-width widget areas
     * @return array
     */

    public function registerModulesWithContainerSupport($modules)
    {
        $modules[] = "mod-posts";
        $modules[] = "mod-location";
        $modules[] = "mod-social";
        $modules[] = "mod-contacts";
        $modules[] = "mod-fileslist";
        $modules[] = "mod-index";
        $modules[] = "mod-inlaylist";
        $modules[] = "mod-form";
        $modules[] = "mod-text";
        $modules[] = "mod-guide";
        $modules[] = "mod-table";
        $modules[] = "mod-gallery";
        $modules[] = "mod-video";
        $modules[] = "mod-notice";
        $modules[] = "mod-g-calendar";

        return $modules;
    }
}
