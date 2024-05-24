<?php

namespace Municipio\PostTypeDesign;

use Municipio\PostTypeDesign\ConfigFromPageId;
use Municipio\PostTypeDesign\ConfigSanitizer;

class SaveDesigns {
    public function __construct(private string $optionName) {}
    
    public function addHooks()
    {
        add_action('customize_save_after', array($this, 'storeDesign'));
    }

    public function storeDesigns(\WP_Customize_Manager $customizerManager = null): void
    {
        
        $postTypes = get_post_types(['public' => true], 'names');
        if (empty($postTypes)) {
            return;
        }
        
        $designOption   = get_option('post_type_design');

        foreach ($postTypes as $postType) {
            $design = get_theme_mod($postType . '_load_design');

            if ($this->hasDesignOrAlreadySetValue($design, $designOption, $postType)) {
                $this->maybeRemoveOptionKey($design, $designOption, $postType);
                continue;
            }

            $this->tryUpdateOptionWithDesign($design, $designOption, $postType);
        }
    }

    public function tryUpdateOptionWithDesign(mixed $design, mixed $designOption, string $postType): void
    {
        [$designConfig, $css]   = (new ConfigFromPageId($design))->get();
        $sanitizedDesignConfig  = $this->getDesignConfig($designConfig);

        if (!empty($sanitizedDesignConfig)) {
            $designOption[$postType] = [
                'design' => $sanitizedDesignConfig, 
                'css' => $css, 
                'designId' => $design
            ];

            update_option('post_type_design', $designOption);
        }
    }

    private function maybeRemoveOptionKey(mixed $design, mixed $designOption, string $postType): void
    {
        if (empty($design) && isset($designOption[$postType])) {
            unset($designOption[$postType]);
            update_option('post_type_design', $designOption);
        }
    }

    private function hasDesignOrAlreadySetValue(mixed $design, mixed $designOption, string $postType): bool
    {
        return empty($design) || 
            (isset($designOption[$postType]) && 
            $designOption[$postType]['designId'] === $design);
    }

    private function getDesignConfig(array $designConfig): array
    {
        $configTransformerInstance = new ConfigSanitizer($designConfig);
        $configTransformerInstance->setKeys(MultiColorKeys::get());
        $configTransformerInstance->setKeys(ColorKeys::get());
        $configTransformerInstance->setKeys(BackgroundKeys::get());

        return $configTransformerInstance->transform();
    }
}