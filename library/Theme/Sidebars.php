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
            'before_widget' => '<div class="left large-6 medium-6 print-6 columns"><div class="footer-content">',
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
            'before_widget' => '<div class="widget">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3>',
            'after_title'   => '</h3>'
        ));

        /**
         * Content Area
         */
        register_sidebar(array(
            'id'            => 'content-area',
            'name'          => __('Below content', 'municipio'),
            'description'   => __('The area below the content', 'municipio'),
            'before_widget' => '<div class="box box-outlined widget %2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Content Area Bottom
         */
        register_sidebar(array(
            'id'            => 'content-area-bottom',
            'name'          => __('Below main container', 'municipio'),
            'description'   => __('The area below the main container', 'municipio'),
            'before_widget' => '<div class="large-12 medium-12 small-12 print-12 columns widget">' .
                               '<div class="box box-outlined %2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Left Sidebar
         */
        register_sidebar(array(
            'id'            => 'left-sidebar',
            'name'          => __('Left sidebar', 'municipio'),
            'description'   => __('The left sidebar area', 'municipio'),
            'before_widget' => '<div class="large-12 medium-12 small-12 print-12 columns widget">' .
                               '<div class="box box-filled widget %2$s">',
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
            'before_widget' => '<div class="large-12 medium-12 small-12 print-12 columns widget">' .
                               '<div class="box box-filled widget %2$s">',
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
            'before_widget' => '<div class="large-12 medium-12 small-12 print-12 columns widget">' .
                               '<div class="box box-filled widget %2$s">',
            'after_widget'  => '</div></div>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>'
        ));
    }
}
