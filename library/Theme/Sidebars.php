<?php

namespace Municipio\Theme;

class Sidebars
{
    public function __construct()
    {
        add_action('widgets_init', array($this, 'register'));
        add_filter('Modularity/Editor/SidebarIncompability', array($this, 'moduleSidebarIncompability'), 10, 2);

        add_filter('Modularity/Module/Container/Sidebars', array($this, 'registerSidebarWithContainerSupport'));
        add_filter('Modularity/Module/Container/Modules', array($this, 'registerModulesWithContainerSupport'));
        
        add_filter('Modularity/Display/BeforeModule', array($this, 'replaceGridClasses'), 10, 1);
    }

    public function replaceGridClasses($beforeMarkup)
    {
        $beforeMarkup = str_replace('grid-md-12', 'o-grid-12@md', $beforeMarkup);
        $beforeMarkup = str_replace('grid-md-9', 'o-grid-9@md', $beforeMarkup);
        $beforeMarkup = str_replace('grid-md-8', 'o-grid-8@md', $beforeMarkup);
        $beforeMarkup = str_replace('grid-md-6', 'o-grid-6@md', $beforeMarkup);
        $beforeMarkup = str_replace('grid-md-4', 'o-grid-4@md', $beforeMarkup);
        $beforeMarkup = str_replace('grid-md-3', 'o-grid-3@md', $beforeMarkup);

        return $beforeMarkup;
    }

    public function register()
    {
        /**
         * Footer Area Top
         */
        register_sidebar(array(
            'id'            => 'footer-area-top',
            'name'          => __('Footer top', 'municipio'),
            'description'   => __('The top of footer area', 'municipio'),
            'before_title'  => '<h2 class="footer-top-title">',
            'after_title'   => '</h2>',
            'before_widget' => '<div class="o-grid-12"><div class="%1$s" id="%2$s">',
            'after_widget'  => '</div></div>'
        ));

        /**
         * Footer Area
         */
        register_sidebar(array(
            'id'            => 'footer-area',
            'name'          => __('Footer', 'municipio'),
            'description'   => __('The footer area', 'municipio'),
            'before_title'  => '<h2 class="footer-title c-typography c-typography__variant--h3">',
            'after_title'   => '</h2>',
            'before_widget' => '<div class="o-grid-4@lg"><div class="%1$s" id="%2$s">',
            'after_widget'  => '</div></div>'
        ));

        /**
         * Slider Area
         */
        register_sidebar(array(
            'id'            => 'slider-area',
            'name'          => __('Hero', 'municipio'),
            'description'   => __('The hero area', 'municipio'),
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Content Area - Top
         */
        register_sidebar(array(
            'id'            => 'content-area-top',
            'name'          => __('Content area (above article)', 'municipio'),
            'description'   => __('The area above the content', 'municipio'),
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Content Area
         */
        register_sidebar(array(
            'id'            => 'content-area',
            'name'          => __('Content area (below article)', 'municipio'),
            'description'   => __('The area below the content', 'municipio'),
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Content Area Bottom
         */
        register_sidebar(array(
            'id'            => 'content-area-bottom',
            'name'          => __('Main container bottom', 'municipio'),
            'description'   => __('The area below the main container', 'municipio'),
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Right Sidebar
         */
        register_sidebar(array(
            'id'            => 'right-sidebar',
            'name'          => __('Right sidebar', 'municipio'),
            'description'   => __('The right sidebar area', 'municipio'),
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Left Sidebar
         */
        register_sidebar(array(
            'id'            => 'left-sidebar',
            'name'          => __('Left sidebar', 'municipio'),
            'description'   => __('The left sidebar area', 'municipio'),
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Left Sidebar Bottom
         */
        register_sidebar(array(
            'id'            => 'left-sidebar-bottom',
            'name'          => __('Left sidebar bottom', 'municipio'),
            'description'   => __('The area below the left sidebar content', 'municipio'),
            'before_title'  => '<h2>',
            'after_title'   => '</h2>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Full page top
         */
        register_sidebar(array(
            'id'            => 'top-sidebar',
            'name'          => __('Top sidebar (full-width)', 'municipio'),
            'description'   => __('Sidebar that sits below the hero, takes up 100% of the widht.', 'municipio'),
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Full page bottom
         */
        register_sidebar(array(
            'id'            => 'bottom-sidebar',
            'name'          => __('Bottom sidebar (full-width)', 'municipio'),
            'description'   => __('Sidebar that sits just before the footer, takes up 100% of the widht.', 'municipio'),
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Floating, top left
         */
        register_sidebar(array(
            'id'            => 'floating-top-left-sidebar',
            'name'          => __('Floating [Top, Left]', 'municipio'),
            'description'   => __('Sidebar that is floating in the top left corner.', 'municipio'),
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Floating, top right
         */
        register_sidebar(array(
            'id'            => 'floating-top-right-sidebar',
            'name'          => __('Floating [Top, Right]', 'municipio'),
            'description'   => __('Sidebar that is floating in the top right corner.', 'municipio'),
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Floating, bottom left
         */
        register_sidebar(array(
            'id'            => 'floating-bottom-left-sidebar',
            'name'          => __('Floating [Bottom, Left]', 'municipio'),
            'description'   => __('Sidebar that is floating in the bottom left corner.', 'municipio'),
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Floating, top right
         */
        register_sidebar(array(
            'id'            => 'floating-bottom-right-sidebar',
            'name'          => __('Floating [Bottom, Right]', 'municipio'),
            'description'   => __('Sidebar that is floating in the bottom right corner.', 'municipio'),
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
            'before_widget' => '<div class="%1$s" id="%2$s">',
            'after_widget'  => '</div>'
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
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-section-full":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-section-split":
                $moduleSpecification['sidebar_incompability'] = array("right-sidebar", "left-sidebar", "left-sidebar-bottom", "footer-area", "content-area-bottom", "content-area", "content-area-top", "footer-area", "footer-area-top");
                break;
            case "mod-slider":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-video":
                $moduleSpecification['sidebar_incompability'] = array("right-sidebar", "left-sidebar", "left-sidebar-bottom", "footer-area", "footer-area-top");
                break;
            case "mod-table":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-gallery":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-guide":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-alarms":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-interactive-map":
                $moduleSpecification['sidebar_incompability'] = array("right-sidebar", "left-sidebar", "left-sidebar-bottom", "footer-area", "slider-area", "bottom-sidebar", "top-sidebar", "footer-area-top");
                break;
            case "mod-posts":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-location":
            case "mod-social":
            case "mod-dictionary":
            case "mod-contacts":
            case "mod-fileslist":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-g-calendar":
            case "mod-index":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-inlaylist":
                $moduleSpecification['sidebar_incompability'] = array("footer-area", "slider-area", "bottom-sidebar", "top-sidebar");
                break;
            case "mod-rss":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-script":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-notice":
            case "mod-iframe":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-event":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-form":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-location":
            case "mod-text":
                $moduleSpecification['sidebar_incompability'] = array("slider-area", "bottom-sidebar", "top-sidebar");
                break;
            case "mod-map":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-timeline":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-sites":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-event-submit":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
                break;
            case "mod-json-render":
                $moduleSpecification['sidebar_incompability'] = array("footer-area-top");
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
