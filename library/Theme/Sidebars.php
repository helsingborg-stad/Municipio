<?php

namespace Municipio\Theme;

class Sidebars
{
    private $footerColumns = null;

    public function __construct()
    {
        add_action('widgets_init', array($this, 'register'));
        add_filter('Modularity/Editor/SidebarIncompability', array($this, 'moduleSidebarIncompability'), 10, 2);

        add_filter('Modularity/Module/Container/Sidebars', array($this, 'registerSidebarWithContainerSupport'));
        add_filter('Modularity/Module/Container/Modules', array($this, 'registerModulesWithContainerSupport'));

        add_filter('Modularity/Display/BeforeModule', array($this, 'replaceGridClasses'), 10, 1);

        add_action('admin_enqueue_scripts', array($this, 'filterVisibleWigets'));

        add_filter('Modularity/Templates/Sidebars', array($this, 'filterAvailableSidebars'));

        add_action('dynamic_sidebar_before', array($this, 'outputBefore'), 1);
        add_action('dynamic_sidebar_after', array($this, 'outputAfter'), 999);
    }

    public function filterAvailableSidebars($sidebars)
    {
        return array_filter($sidebars, function ($sidebar) {
            return strpos($sidebar['id'], 'footer-') !== 0;
        });
    }

    public function filterVisibleWigets($page)
    {
        if ('widgets.php' !== $page) {
            return;
        }
        $footerStyle   = \Kirki::get_option(\Municipio\Customizer::KIRKI_CONFIG, 'footer_style');
        $footerColumns = \Kirki::get_option(\Municipio\Customizer::KIRKI_CONFIG, 'footer_columns');
        wp_enqueue_script(
            'widgets-area-hide-js',
            get_template_directory_uri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/widgets-area-hider.js')
        );
        wp_localize_script(
            'widgets-area-hide-js',
            'municipioSidebars',
            [
                'footerStyle'   => $footerStyle,
                'footerColumns' => $footerColumns ?? 1,
            ]
        );
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
        $beforeWidget        = '<div id="%1$s" class="%2$s">';
        $afterWidget         = '</div>';
        $beforeModulesMarkup = '<aside class="o-grid">';
        $afterModulesMarkup  = '</aside>';

        /**
         * Footer Area Top
         */
        register_sidebar(array(
            'id'            => 'footer-area-top',
            'name'          => __('Footer top', 'municipio'),
            'description'   => __('The top of footer area', 'municipio'),
            'before_title'  => '<h2 class="footer-top-title">',
            'after_title'   => '</h2>',
            'before_widget' => '<div class="o-grid-12">' . $beforeWidget,
            'after_widget'  => $afterWidget . '</div>',
        ));

        /**
         * Create a total of 6 footer areas for use with the "columns" footer style
         */
        $footerStyle    = \Kirki::get_option(\Municipio\Customizer::KIRKI_CONFIG, 'footer_style') ?? 'basic';
        $footerGridSize = $footerStyle === 'basic' ? 4 : 12;
        for ($i = 0; $i < 6; $i++) {
            $suffix = ($i !== 0 ? '-column-' . $i : '');
            register_sidebar(array(
                'id'            => 'footer-area' . $suffix,
                'name'          => __('Footer area', 'municipio') . ' (' . ($i + 1) . ')',
                'description'   => __('The footer area ' . $suffix, 'municipio'),
                'before_title'  => '<h2 class="footer-title c-typography c-typography__variant--h3">',
                'after_title'   => '</h2>',
                'before_widget' => '<div class="o-grid-' . $footerGridSize . '@md' . ' o-grid-12">' . $beforeWidget,
                'after_widget'  => $afterWidget . '</div>',
            ));
        }

        /**
         * Footer Area Bottom
         */
        register_sidebar(array(
            'id'            => 'footer-area-bottom',
            'name'          => __('Footer bottom', 'municipio'),
            'description'   => __('The bottom of footer area', 'municipio'),
            'before_title'  => '<h2 class="footer-bottom-title">',
            'after_title'   => '</h2>',
            'before_widget' => '<div class="o-grid-12">' . $beforeWidget,
            'after_widget'  => $afterWidget . '</div>',
        ));

        /**
         * Slider Area
         */
        register_sidebar(array(
            'id'            => 'slider-area',
            'name'          => __('Hero', 'municipio'),
            'description'   => __('The hero area', 'municipio'),
            'before_title'  => '<h2 class="c-typography c-typography__variant--h3">',
            'after_title'   => '</h2>',
            'before_widget' => $beforeWidget,
            'after_widget'  => $afterWidget,
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
            'before_widget' => $beforeWidget,
            'after_widget'  => $afterWidget,
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
            'before_widget' => $beforeWidget,
            'after_widget'  => $afterWidget,
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
            'before_widget' => $beforeWidget,
            'after_widget'  => $afterWidget,
        ));

        /**
         * Right Sidebar
         */
        register_sidebar(array(
            'id'             => 'right-sidebar',
            'name'           => __('Right sidebar', 'municipio'),
            'description'    => __('The right sidebar area', 'municipio'),
            'before_title'   => '<h2>',
            'after_title'    => '</h2>',
            'before_widget'  => $beforeWidget,
            'after_widget'   => $afterWidget,
            'before_modules' => $beforeModulesMarkup,
            'after_modules'  => $afterModulesMarkup,
        ));

        /**
         * Left Sidebar
         */
        register_sidebar(array(
            'id'             => 'left-sidebar',
            'name'           => __('Left sidebar', 'municipio'),
            'description'    => __('The left sidebar area', 'municipio'),
            'before_title'   => '<h2>',
            'after_title'    => '</h2>',
            'before_widget'  => $beforeWidget,
            'after_widget'   => $afterWidget,
            'before_modules' => $beforeModulesMarkup,
            'after_modules'  => $afterModulesMarkup,
        ));

        /**
         * Left Sidebar Bottom
         */
        register_sidebar(array(
            'id'             => 'left-sidebar-bottom',
            'name'           => __('Left sidebar bottom', 'municipio'),
            'description'    => __('The area below the left sidebar content', 'municipio'),
            'before_title'   => '<h2>',
            'after_title'    => '</h2>',
            'before_widget'  => $beforeWidget,
            'after_widget'   => $afterWidget,
            'before_modules' => $beforeModulesMarkup,
            'after_modules'  => $afterModulesMarkup,
        ));

        /**
         * Full page top
         */
        register_sidebar(array(
            'id'            => 'top-sidebar',
            'name'          => __('Top sidebar (full-width)', 'municipio'),
            'description'   => __('Sidebar that sits below the hero.', 'municipio'),
            'before_title'  => '<h2 class="c-typography c-typography__variant--h3">',
            'after_title'   => '</h2>',
            'before_widget' => $beforeWidget,
            'after_widget'  => $afterWidget,
        ));

        /**
         * Full page bottom
         */
        register_sidebar(array(
            'id'            => 'bottom-sidebar',
            'name'          => __('Bottom sidebar (full-width)', 'municipio'),
            'description'   => __('Sidebar that sits just before the footer.', 'municipio'),
            'before_title'  => '<h2 class="c-typography c-typography__variant--h3">',
            'after_title'   => '</h2>',
            'before_widget' => $beforeWidget,
            'after_widget'  => $afterWidget,
        ));

        /**
         * Above columns
         */
        register_sidebar(array(
            'id'            => 'above-columns-sidebar',
            'name'          => __('Above columns sidebar', 'municipio'),
            'description'   => __('Sidebar that sits just before the columns grid.', 'municipio'),
            'before_title'  => '<h2 class="c-typography c-typography__variant--h3">',
            'after_title'   => '</h2>',
            'before_widget' => $beforeWidget,
            'after_widget'  => $afterWidget,
        ));
    }

    public function outputBefore($sidebar)
    {
        global $wp_registered_sidebars;
        if (!empty($wp_registered_sidebars[$sidebar]['before_modules'])) {
            echo $wp_registered_sidebars[$sidebar]['before_modules'];
        }
    }
    public function outputAfter($sidebar)
    {
        global $wp_registered_sidebars;
        if (!empty($wp_registered_sidebars[$sidebar]['after_modules'])) {
            echo $wp_registered_sidebars[$sidebar]['after_modules'];
        }
    }

    /**
     * Appends compability support
     * @param  array $moduleSpecification Original module settings
     * @return array
     */

    public function moduleSidebarIncompability($moduleSpecification, $modulePostType): array
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
