<?php

namespace Municipio\PostTypeDesign;

use Municipio\PostTypeDesign\ConfigFromPageId;
use Municipio\PostTypeDesign\ConfigTransformer;

class SaveDesigns {
    public function __construct(private string $optionName) 
    {
        add_action('customize_save_after', array($this, 'storeDesign'));
    }

    public function storeDesign($customizerManager = null) 
    {
        $postTypes = get_post_types(['public' => true], 'names');
        if (empty($postTypes)) {
            return;
        }
        
        $designOption   = get_option('post_type_design');
        foreach ($postTypes as $postType) {
            $design = get_theme_mod($postType . '_load_design');

            if (empty($design) || isset($designOption[$postType])) {
                continue;
            }

            $designConfig = (new ConfigFromPageId($design))->get();

            $configTransformerInstance = new ConfigTransformer($designConfig);
            $configTransformerInstance->setKeys(MultiColorKeys::get());
            $designConfig = $configTransformerInstance->transform();

            if (!empty($designConfig)) {
                $designOption[$postType] = $designConfig;
                update_option('post_type_design', $designOption);
            }
        }
    }
}