<?php
namespace Municipio\Admin\Gutenberg\Blocks;

use Municipio\Template as Template;

class BlockManager {
    public function __construct() {
        add_filter('Municipio/blade/view_paths', array($this, 'getViewPath'), 10);
        $this->registerBlocks();
    }

    //Register block
    public function registerBlocks() {
        // Check function exists.
        if( function_exists('acf_register_block_type') ) {

            // register a testimonial block.
            acf_register_block_type(array(
                'name'              => 'button',
                'title'             => __('Button', 'municipio'),
                'description'       => __('A button block', 'municipio'),
                'render_callback'   => array($this, 'renderCallback'),
                'category'          => 'formatting',
                'icon'              => 'button',
                'keywords'          => array('button', 'link'),
                'supports'          => [
                    'align' => true
                ],
                'view'              => 'button'
            ));

            acf_register_block_type(array(
                'name'              => 'classic',
                'title'             => __('Classic', 'municipio'),
                'description'       => __('A block that lets you create and edit articles', 'municipio'),
                'render_callback'   => array($this, 'renderCallback'),
                'category'          => 'formatting',
                'icon'              => 'text',
                'keywords'          => array('editor', 'classic'),
                'supports'          => [
                    'align' => true
                ],
                'view'              => 'classic'
            ));
        }
    }

    //Callback for render, builds view with blade engine
    public function renderCallback($block) {
        $data = $this->buildData($block['data']);
        $data['classList'] = $this->buildBlockClassList($block);
        $template = new Template();        

        if($this->validateFields($block['data'])) {
            $template->renderView($block['view'], $data);
        } else {
            $template->renderView('default', ['blockTitle' => $block['title'], 'message' => __('Please fill in all required fields.')]);
        }

    }

    //Returns view path
    public function getViewPath($paths) {
        $paths[] = plugin_dir_path( __FILE__ ) . 'views';

        return $paths;
    }

    // Build data my getting value from field id and format key
    public function buildData($data) {
        $newData = [];
        foreach($data as $key => $value) {
            $key = ltrim($key, '_');
            
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

        if(isset($block['align'])) {
            $classList[] = "t-block-align-" . $block['align'];
        }

        return implode(' ', $classList);
    }

    /**
         * Validates the required fields
         * @return boolean
         */
        private function validateFields($fields) {        
            
            $valid = true;            

            foreach($fields as $key => $value) {    
                
                if(is_string($key) && is_string($value)) {
                    if(str_contains($key, 'field_')) {
                        $field = $key;
                    } elseif(str_contains($value, 'field_')) {
                        $field = $value;
                    }
                }

                $fieldObject = get_field_object($field);
                //Skip validation of decendants
                if(isset($fieldObject['parent']) && str_contains($fieldObject['parent'], 'field_')) {
                    continue;
                }
                
                //Check if required field has a value
                if($fieldObject['required'] && (!$fieldObject['value'] && $fieldObject['value'] !== "0")) {
                    $valid = false;
                }
                
            }

            return $valid;
        }
}