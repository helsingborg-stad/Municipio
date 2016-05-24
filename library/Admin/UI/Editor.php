<?php

namespace Municipio\Admin\UI;

class Editor
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'init'));
        add_action('admin_footer', array($this, 'getMetakeys'));
    }

    public function init()
    {
        if (!current_user_can('edit_posts') || !current_user_can('edit_pages')) {
            return;
        }

        add_filter('mce_external_plugins', array($this, 'registerMcePlugin'));
        add_filter('mce_buttons', array($this, 'registerButtons'));
    }

    public function registerMcePlugin($plugins)
    {
        $plugins['metadata'] = get_template_directory_uri() . '/assets/dist/js/mce-metadata.js';
        return $plugins;
    }

    public function registerButtons($buttons)
    {
        array_push($buttons, 'metadata');
        return $buttons;
    }

    public function getMetakeys()
    {
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
    }
}
