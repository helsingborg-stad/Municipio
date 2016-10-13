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
        // Set color scheme class on mce body
        $color = get_field('color_scheme', 'option');

        if ($color) {
            $settings['body_class'] .= ' theme-' . $color;
        }

        // Get style formats
        $styleFormats = self::getEnabledStyleFormats();

        if (empty($styleFormats)) {
            return $settings;
        }

        $settings['style_formats'] = json_encode($styleFormats);
        return $settings;
    }

    public static function getEnabledStyleFormats()
    {
        $returnFormats = array();
        $formats = self::getAvailableStyleFormats();
        $enabled = get_field('content_editor_formats', 'options');

        foreach ($formats as $key => &$format) {
            $format = array_filter($format, function ($key) use ($enabled) {
                return in_array($key, $enabled);
            }, ARRAY_FILTER_USE_KEY);
        }

        foreach ($formats as $key => $items) {
            if (!is_array($items) || count($items) === 0) {
                continue;
            }

            $returnFormats[] = array(
                'title' => $key,
                'items' => $items
            );
        }

        return $returnFormats;
    }

    public static function getAvailableStyleFormats()
    {
        return apply_filters('Municipio\WpEditor\AvailableFormats', array(
            'Text size' => array(
                'small' => array(
                    'title' => 'Small',
                    'inline' => 'small'
                ),
                'large' => array(
                    'title' => 'Large',
                    'inline' => 'span',
                    'classes' => 'text-lg'
                ),
                'extra-large' => array(
                    'title' => 'Extra large',
                    'inline' => 'span',
                    'classes' => 'text-xl'
                )
            ),

            'Text transform' => array(
                'uppercase' => array(
                    'title' => 'Uppercase',
                    'inline' => 'span',
                    'classes' => 'text-uppercase'
                ),
                'lowercase' => array(
                    'title' => 'Lowercase',
                    'inline' => 'span',
                    'classes' => 'text-lowercase'
                ),
                'capitalize' => array(
                    'title' => 'Capitalize',
                    'inline' => 'span',
                    'classes' => 'text-capitalize'
                )
            ),

            'Highlight' => array(
                'dark-gray' => array(
                    'title' => 'Drak gray',
                    'inline' => 'span',
                    'classes' => 'text-dark-gray'
                ),
                'highlight' => array(
                    'title' => 'Highlight',
                    'inline' => 'span',
                    'classes' => 'text-highlight'
                ),
                'mark' => array(
                    'title' => 'Mark',
                    'inline' => 'mark',
                    'classes' => 'mark'
                ),
                'mark-yellow' => array(
                    'title' => 'Mark yellow',
                    'inline' => 'mark',
                    'classes' => 'mark-yellow'
                ),
                'mark-blue' => array(
                    'title' => 'Mark blue',
                    'inline' => 'mark',
                    'classes' => 'mark-blue'
                ),
                'mark-green' => array(
                    'title' => 'Mark green',
                    'inline' => 'mark',
                    'classes' => 'mark-green'
                ),
                'mark-red' => array(
                    'title' => 'Mark red',
                    'inline' => 'mark',
                    'classes' => 'mark-red'
                ),
                'mark-purple' => array(
                    'title' => 'Mark purple',
                    'inline' => 'mark',
                    'classes' => 'mark-purple'
                ),
            ),

            'Buttons' => array(
                'button' => array(
                    'title' => 'Button',
                    'inline' => 'a',
                    'classes' => 'btn btn-md btn-btn'
                ),
                'button-primary' => array(
                    'title' => 'Primary button',
                    'inline' => 'a',
                    'classes' => 'btn btn-md btn-primary'
                ),
                'button-first' => array(
                    'title' => 'Button: First color',
                    'inline' => 'a',
                    'classes' => 'btn btn-md btn-theme-first'
                ),
                'button-second' => array(
                    'title' => 'Button: Second color',
                    'inline' => 'a',
                    'classes' => 'btn btn-md btn-theme-second'
                ),
                'button-third' => array(
                    'title' => 'Button: Third color',
                    'inline' => 'a',
                    'classes' => 'btn btn-md btn-theme-third'
                ),
                'button-fourth' => array(
                    'title' => 'Button: Fourth color',
                    'inline' => 'a',
                    'classes' => 'btn btn-md btn-theme-fourth'
                ),
                'button-fifth' => array(
                    'title' => 'Button: Fifth color',
                    'inline' => 'a',
                    'classes' => 'btn btn-md btn-theme-fifth'
                ),
            )
        ));
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

        add_filter('mce_external_plugins', function ($plugins) {
            $plugins['metadata'] = get_template_directory_uri() . '/assets/dist/js/mce-metadata.js';
            return $plugins;
        });

        add_filter('mce_buttons_2', function ($buttons) {
            array_splice($buttons, 2, 0, array('metadata'));
            return $buttons;
        });
    }
}
