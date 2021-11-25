<?php

namespace Municipio\Admin\Gutenberg;

class Gutenberg
{
    public function __construct()
    {
        add_filter("use_block_editor_for_post_type", array($this, 'activateGutenbergEditor'), 10, 2);
    }

    public function activateGutenbergEditor($useBlockEditor, $postType) {

        global $post;

        if (!is_admin()) {
            return $useBlockEditor;
        }

        //Get current mode
        $gutenbergEditorMode = get_field('gutenberg_editor_mode', 'option') ?? 'disabled';

        if ($gutenbergEditorMode === 'disabled') {
            $useBlockEditor = false;
        }

        if ($gutenbergEditorMode === 'all') {
            $useBlockEditor = true;
        }

        //Enable for specific templates
        if (is_a($post, 'WP_Post') && $gutenbergEditorMode === 'template') {
            $template = get_post_meta($post->ID, '_wp_page_template', true);

            if (in_array($template, ['one-page.blade.php'])) {
                $useBlockEditor = true;
            } else {
                $useBlockEditor = false;
            }
        }

        return $useBlockEditor;
    }

}