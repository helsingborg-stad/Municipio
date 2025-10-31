<?php

namespace Municipio\Admin\Gutenberg;

/**
* Class Gutenberg
*
*
*/
class Gutenberg
{
        /**
         * Constructor method.
         *
         * Initializes the Gutenberg class and adds necessary filters.
         */
    public function __construct()
    {
        add_filter("use_block_editor_for_post_type", array($this, 'activateGutenbergEditor'), 10, 2);
        //add_filter('allowed_block_types', [$this,'additionalAllowedBlocks'], 10, 1);
    }


    /**
     *
     * @param array $allowed_block_types The array of allowed block types.
     * @return array The updated array of allowed block types.
     */
    public function additionalAllowedBlocks($allowed_block_types)
    {
        $allowed_block_types[] = 'core/embed';
        return $allowed_block_types;
    }
    /**
     * Activates the Gutenberg editor based on the specified conditions.
     *
     * @param bool $useBlockEditor Whether to use the block editor.
     * @param string $postType The post type.
     * @return bool The updated value of $useBlockEditor.
     */

    public function activateGutenbergEditor($useBlockEditor, $postType)
    {

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

            $templatesToInclude = apply_filters(
                'Municipio/Admin/Gutenberg/TemplatesToInclude',
                ['one-page.blade.php']
            );

            if (in_array($template, $templatesToInclude)) {
                $useBlockEditor = true;
            } else {
                $useBlockEditor = false;
            }
        }

        return $useBlockEditor;
    }
}
