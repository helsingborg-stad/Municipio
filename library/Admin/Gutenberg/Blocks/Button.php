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

            // register a button block.
            acf_register_block_type(array(
                'name'              => 'button',
                'title'             => __('Button', 'municipio'),
                'description'       => __('A button block', 'municipio'),
                'render_callback'   => array($this, 'renderCallback'),
                'category'          => 'formatting',
                'icon'              => 'button',
                'keywords'          => array('button', 'link'),
                'supports'          => [
                    'align' => true,
                    'jsx' => true
                ]
            ));

            // register a button block.
            acf_register_block_type(array(
                'name'              => 'innerButton',
                'title'             => __('Button (Inner)', 'municipio'),
                'description'       => __('A button block', 'municipio'),
                'render_callback'   => array($this, 'renderCallback'),
                'category'          => 'formatting',
                'icon'              => 'button',
                'keywords'          => array('button', 'link'),
                'parent'            => ['acf/button'],
                'supports'          => [
                    'align' => false,
                    'jsx' => true
                ]
            ));
        }
    }

    //Callback for render, builds view with blade engine
    public function renderCallback($block) {

        $data = $this->buildData($block['data']);
        $data['classList'] = $this->buildBlockClassList($block);

        $template = new Template();

        $data['blockType'] = $block['name']; 

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
            $newValue = get_field($value);
            
            if(str_contains($value, 'field_')) {
                $newData[$key] = get_field($value);
            } else {
                $newData[get_field_object($key)['name']] = $value;
            }
        }

        return $newData;
    }

    public function buildBlockClassList($block)
    {
        $classList = ['t-block-container'];

        if(in_array($block['name'], ['acf/button'])) {
            $classList[] = "t-block-button"; 
        }

        if(isset($block['align']) && !empty($block['align'])) {
            $classList[] = "t-block-align-" . $block['align'];
        }

        return implode(' ', $classList);
    }
}
