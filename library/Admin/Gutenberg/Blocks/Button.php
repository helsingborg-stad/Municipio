<?php
namespace Municipio\Admin\Gutenberg\Blocks;

use Municipio\Template as Template;

class Button {
    public function __construct() {
        add_action('admin_init', array($this, 'municipio_button_block'), 1);
    }

    public function municipio_button_block() {
        // Check function exists.
        if( function_exists('acf_register_block_type') ) {
                            // 'render_template'   => plugin_dir_path( __FILE__ ) . 'vButton.php',

            // register a testimonial block.
            acf_register_block_type(array(
                'name'              => 'button',
                'title'             => __('Button'),
                'description'       => __('A button block'),
                'render_callback'   => [$this, 'renderCallback'],
                'category'          => 'formatting',
                'icon'              => 'dashicons-button',
                'keywords'          => array( 'testimonial', 'quote' ),
            ));
        }
    }

    public function renderCallback() {
        $template = new Template();
        $template->renderView(plugin_dir_path( __FILE__ ) . 'vButton.blade.php', []);
    }
}