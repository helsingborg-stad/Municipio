<?php
namespace Municipio\Admin\Gutenberg\Blocks;

use Municipio\Template as Template;

class Button {
    public function __construct() {
        add_filter('Municipio/blade/view_paths', array($this, 'getViewPath'), 10);
        add_action('admin_init', array($this, 'municipio_button_block'), 1);
    }

    //Register block
    public function municipio_button_block() {
        // Check function exists.
        if( function_exists('acf_register_block_type') ) {

            // register a testimonial block.
            acf_register_block_type(array(
                'name'              => 'button',
                'title'             => __('Button'),
                'description'       => __('A button block'),
                'render_callback'   => array($this, 'renderCallback'),
                'category'          => 'formatting',
                'icon'              => 'dashicons-button',
                'keywords'          => array( 'testimonial', 'quote' ),
            ));
        }
    }

    //Callback for render, builds view with blade engine
    public function renderCallback($block) {
        $data = $this->buildData($block['data']);
        $template = new Template();
        $template->renderView('button', $data);
    }

    //Returns view path
    public function getViewPath($paths) {
        $paths[] = plugin_dir_path( __FILE__ );

        return $paths;
    }

    // Build data my getting value from field id and format key
    public function buildData($data) {
        $newData = [];

        foreach($data as $key => $value) {
            $key = ltrim($key, '_');
            $newData[$key] = get_field($value);
        }

        return $newData;
    }
}