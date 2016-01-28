<?php

namespace Municipio\Theme;

class Sidebars
{
    public function __construct()
    {
        add_action('widgets_init', array($this, 'register'));
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
            'before_widget' => '<div class="grid-lg-4 %2$s">',
            'after_widget'  => '</div>',
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
            'before_widget' => '<div class="grid-sm-12 %2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Content Area Bottom
         */
        register_sidebar(array(
            'id'            => 'content-area-bottom',
            'name'          => __('Main container bottom', 'municipio'),
            'description'   => __('The area below the main container', 'municipio'),
            'before_widget' => '<div class="grid-sm-12 grid-md-6 grid-lg-6 %2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Left Sidebar
         */
        register_sidebar(array(
            'id'            => 'left-sidebar',
            'name'          => __('Left sidebar', 'municipio'),
            'description'   => __('The left sidebar area', 'municipio'),
            'before_widget' => '<div class="grid-lg-12 %2$s">',
            'after_widget'  => '</div>',
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
            'before_widget' => '<div class="grid-lg-12 %2$s">',
            'after_widget'  => '</div>',
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
            'before_widget' => '<div class="grid-lg-12">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>'
        ));
    }
}
