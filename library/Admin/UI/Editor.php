<?php

namespace Municipio\Admin\UI;

class Editor
{
    public function __construct()
    {
        // Add editor stylesheet
        add_action('admin_init', function () {
            if ((defined('DEV_MODE') && DEV_MODE === true) || (isset($_GET['DEV_MODE']) && $_GET['DEV_MODE'] === 'true')) {
                add_editor_style(apply_filters('Municipio/admin/editor_stylesheet', '//hbgprime.dev/dist/css/hbg-prime.min.css'));
            } else {
                add_editor_style(apply_filters('Municipio/admin/editor_stylesheet', '//helsingborg-stad.github.io/styleguide-web-cdn/styleguide.dev/dist/css/hbg-prime.min.css'));
            }
        });

        // Custom plugins
        add_action('admin_init', array($this, 'metaData'));

        // Add format dropdown
        add_filter('mce_buttons_2', function ($buttons) {
            array_unshift($buttons, 'styleselect');
            return $buttons;
        });

        // Add the formats
        add_filter('tiny_mce_before_init', array($this, 'styleFormat'));
    }

    /**
     * Add style format options
     * @param  array $settings  Options array
     * @return array            Modified options array
     */
    public function styleFormat($settings)
    {
        $styleFormats = array(
            // Text sizes
            array(
                'title' => 'Text size',
                'items' => array(
                    array(
                        'title' => 'Small',
                        'inline' => 'small'
                    ),
                    array(
                        'title' => 'Large',
                        'inline' => 'span',
                        'classes' => 'text-lg'
                    ),
                    array(
                        'title' => 'Extra large',
                        'inline' => 'span',
                        'classes' => 'text-xl'
                    )
                )
            ),

            // Transform
            array(
                'title' => 'Transform',
                'items' => array(
                    array(
                        'title' => 'Uppercase',
                        'inline' => 'span',
                        'classes' => 'text-uppercase'
                    ),
                    array(
                        'title' => 'Lowercase',
                        'inline' => 'span',
                        'classes' => 'text-lowercase'
                    ),
                    array(
                        'title' => 'Capitalize',
                        'inline' => 'span',
                        'classes' => 'text-capitalize'
                    )
                )
            ),

            // Text markings
            array(
                'title' => 'Highlight',
                'items' => array(
                    array(
                        'title' => 'Drak gray',
                        'inline' => 'span',
                        'classes' => 'text-dark-gray'
                    ),
                    array(
                        'title' => 'Highlight',
                        'inline' => 'span',
                        'classes' => 'text-highlight'
                    ),
                    array(
                        'title' => 'Mark',
                        'inline' => 'mark',
                        'classes' => 'mark'
                    ),
                    array(
                        'title' => 'Mark yellow',
                        'inline' => 'mark',
                        'classes' => 'mark-yellow'
                    ),
                    array(
                        'title' => 'Mark blue',
                        'inline' => 'mark',
                        'classes' => 'mark-blue'
                    ),
                    array(
                        'title' => 'Mark green',
                        'inline' => 'mark',
                        'classes' => 'mark-green'
                    ),
                    array(
                        'title' => 'Mark red',
                        'inline' => 'mark',
                        'classes' => 'mark-red'
                    ),
                    array(
                        'title' => 'Mark purple',
                        'inline' => 'mark',
                        'classes' => 'mark-purple'
                    ),
                )
            ),

            // Buttons
            array(
                'title' => 'Buttons',
                'items' => array(
                    array(
                        'title' => 'Button',
                        'inline' => 'a',
                        'classes' => 'btn btn-md btn-btn'
                    ),
                    array(
                        'title' => 'Primary button',
                        'inline' => 'a',
                        'classes' => 'btn btn-md btn-primary'
                    ),
                    array(
                        'title' => 'Button: First color',
                        'inline' => 'a',
                        'classes' => 'btn btn-md btn-theme-first'
                    ),
                    array(
                        'title' => 'Button: Second color',
                        'inline' => 'a',
                        'classes' => 'btn btn-md btn-theme-second'
                    ),
                    array(
                        'title' => 'Button: Third color',
                        'inline' => 'a',
                        'classes' => 'btn btn-md btn-theme-third'
                    ),
                    array(
                        'title' => 'Button: Fourth color',
                        'inline' => 'a',
                        'classes' => 'btn btn-md btn-theme-fourth'
                    ),
                    array(
                        'title' => 'Button: Fifth color',
                        'inline' => 'a',
                        'classes' => 'btn btn-md btn-theme-fifth'
                    ),
                )
            ),
        );

        $settings['style_formats'] = json_encode($styleFormats);

        // Set color scheme class on mce body
        $color = get_field('color_scheme', 'option');

        if ($color) {
            $settings['body_class'] .= ' theme-' . $color;
        }

        return $settings;
    }

    /**
     * Metadata plugin
     * @return void
     */
    public function metaData()
    {
        global $pagenow;

        if (!current_user_can('edit_posts') || !current_user_can('edit_pages') || $pagenow != 'post.php') {
            return;
        }

        add_action('admin_footer', function () {
            global $post;
            $metakeys = \Municipio\Helper\Post::getPostMetaKeys($post->ID);

            echo '<script>
                    var metadata_button = [
            ';

            $count = 0;
            foreach ($metakeys as $key => $value) {
                echo "{text: '{$value->meta_key}', value: '[meta key=\"{$value->meta_key}\"]'},";
                $count++;
            }

            echo '];</script>';
        });

        add_filter('mce_external_plugins', function ($plugin) {
            $plugins['metadata'] = get_template_directory_uri() . '/assets/dist/js/mce-metadata.js';
            return $plugins;
        });

        add_filter('mce_buttons', function ($buttons) {
            array_push($buttons, 'metadata');
            return $buttons;
        });
    }
}
