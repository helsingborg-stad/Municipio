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
            'name'          => __('Footerarea', 'Helsingborg'),
            'description'   => __('Arean längst ner på sidan', 'Helsingborg'),
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
            'name'          => __('Topparea', 'Helsingborg'),
            'description'   => __('Visas under huvudmenyn', 'Helsingborg'),
            'before_widget' => '<div class="large-12 medium-12 small-12 print-12 columns widget">' .
                               '<div class="box %2$s">',
            'after_widget'  => '</div></div>',
            'before_title'  => '<h3>',
            'after_title'   => '</h3>'
        ));

        /**
         * Content Area
         */
        register_sidebar(array(
            'id'            => 'content-area',
            'name'          => __('Innehållsarea', 'Helsingborg'),
            'description'   => __('Visas strax under en artikels brödtext', 'Helsingborg'),
            'before_widget' => '<div class="box box-outlined widget %2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Content Area Bottom
         */
        register_sidebar(array(
            'id'            => 'content-area-bottom',
            'name'          => __('Innehåll bottenarea', 'Helsingborg'),
            'description'   => __('Visas under vänstermeny och artikel (fullbredd) ', 'Helsingborg'),
            'before_widget' => '<div class="large-12 medium-12 small-12 print-12 columns widget">' .
                               '<div class="box box-outlined %2$s">',
            'after_widget'  => '</div>'
        ));

        /**
         * Service Area
         */
        register_sidebar(array(
            'id'            => 'service-area',
            'name'          => __('Servicearea', 'Helsingborg'),
            'description'   => __('De service-länkar som visas i grått fält på startsidan', 'Helsingborg'),
            'before_widget' => '<div class="widget columns large-4 medium-4 small-12 pprint-4">',
            'after_widget'  => '</div>'
        ));

        /**
         * Fun Facts Area
         */
        register_sidebar(array(
            'id'            => 'fun-facts-area',
            'name'          => __('Fakta', 'Helsingborg'),
            'description'   => __('Faktarutor som visas innan footer (visar tre slumpmässiga).', 'Helsingborg'),
            'before_widget' => '<div class="widget columns large-3 medium-3 print-3 left">',
            'after_widget'  => '</div>'
        ));

        /**
         * Left Sidebar
         */
        register_sidebar(array(
            'id'            => 'left-sidebar',
            'name'          => __('Vänster area', 'Helsingborg'),
            'description'   => __('Visas ovanför vänstermenyn.', 'Helsingborg'),
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
            'name'          => __('Vänster bottenarea', 'Helsingborg'),
            'description'   => __('Visas under vänstermenyn.', 'Helsingborg'),
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
            'name'          => __('Höger area', 'Helsingborg'),
            'description'   => __('Visas i högerspalten.', 'Helsingborg'),
            'before_widget' => '<div class="large-12 medium-12 small-12 print-12 columns widget">' .
                               '<div class="box box-filled widget %2$s">',
            'after_widget'  => '</div></div>',
            'before_title'  => '<h2>',
            'after_title'   => '</h2>'
        ));
    }
}
