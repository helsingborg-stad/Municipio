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

        // Metadata plugin
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
            array(
                'title' => 'Small',
                'inline' => 'small'
            )
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
