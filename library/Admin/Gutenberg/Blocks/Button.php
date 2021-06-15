<?php
namespace Municipio\Admin\Gutenberg\Blocks;

use Municipio\Template as Template;

class Button {
    public function __construct() {
        add_filter('Municipio/blade/view_paths', array($this, 'getViewPath'), 10);
        $this->municipioButtonBlock();
    }

    //Register block
    public function municipioButtonBlock() {
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
        echo '<pre>', print_r($data), '</pre>';


        foreach($data as $key => $value) {
            $key = ltrim($key, '_');
            $newValue = get_field($value);
            
            if(str_contains($value, 'field_')) {
                $newData[$key] = get_field($value);
            } else {
                $newData[get_field_object($key)['name']] = $value;
            }


        }

        return $newData;
    }
}