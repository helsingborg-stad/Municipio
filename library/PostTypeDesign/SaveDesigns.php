<?php

namespace Municipio\PostTypeDesign;

use Municipio\PostTypeDesign\ConfigFromPageId;
use Municipio\PostTypeDesign\ConfigSanitizer;

class SaveDesigns {
    public function __construct(private string $optionName) 
    {
        add_action('wp', array($this, 'storeDesign'));
        // add_action('customize_save_after', array($this, 'storeDesign'));
    }

    public function storeDesign($customizerManager = null) 
    {
        
        $postTypes = get_post_types(['public' => true], 'names');
        if (empty($postTypes)) {
            return;
        }
        
        // delete_option('post_type_design');
        $designOption   = get_option('post_type_design');

        foreach ($postTypes as $postType) {
            $design = get_theme_mod($postType . '_load_design');

            if (
                empty($design) || 
                (isset($designOption[$postType]) && $designOption[$postType]['designId'] === $design)
            ) {
                if (empty($design) && isset($designOption[$postType])) {
                    unset($designOption[$postType]);
                    update_option('post_type_design', $designOption);
                }
                
                continue;
            }

            [$designConfig, $css] = (new ConfigFromPageId($design))->get();

            $configTransformerInstance = new ConfigSanitizer($designConfig);
            $configTransformerInstance->setKeys(MultiColorKeys::get());
            $configTransformerInstance->setKeys(ColorKeys::get());
            $configTransformerInstance->setKeys(BackgroundKeys::get());
            $designConfig = $configTransformerInstance->transform();

            if (!empty($designConfig)) {
                $designOption[$postType] = [
                    'design' => $designConfig, 
                    'css' => $css, 
                    'designId' => $design
                ];

                update_option('post_type_design', $designOption);
            }
        }
    }
}