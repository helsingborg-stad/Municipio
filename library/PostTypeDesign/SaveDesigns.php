<?php

namespace Municipio\PostTypeDesign;

use Municipio\Customizer\PanelsRegistry;
use Municipio\PostTypeDesign\InlineCssGenerator;
use Municipio\PostTypeDesign\GetFields;
use Municipio\HooksRegistrar\Hookable;
use Municipio\PostTypeDesign\ConfigSanitizer;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\UpdateOption;

/**
 * Class SaveDesigns
 *
 * This class is responsible for saving designs for post types.
 */
class SaveDesigns implements Hookable
{
    public array $designOption;

    /**
     * SaveDesigns constructor.
     */
    public function __construct(
        private string $optionName,
        private AddAction&GetOption&GetThemeMod&GetPostTypes&UpdateOption $wpService,
        private ConfigFromPageIdInterface $configFromPageId
    ) {
        $this->designOption = [];
    }

    /**
     * Add hooks for saving designs.
     */
    public function addHooks(): void
    {
        // $this->wpService->addAction('customize_save_after', array($this, 'storeDesigns'));
        $this->wpService->addAction('wp', array($this, 'storeDesigns'));
    }

    /**
     * Store designs for post types.
     */
    public function storeDesigns(): void
    {
        $postTypes = $this->wpService->getPostTypes(['public' => true], 'names');
        if (empty($postTypes)) {
            return;
        }

        // $this->designOption = $this->wpService->getOption($this->optionName) ?? [];
        $this->designOption = [];
        $getFieldsInstance  = new GetFields(PanelsRegistry::getInstance()->getRegisteredFields());

        foreach ($postTypes as $postType) {
            $design = $this->wpService->getThemeMod($postType . '_load_design');

            if ($this->hasDesignOrAlreadySetValue($design, $postType)) {
                $this->maybeRemoveOptionKey($design, $postType);
                continue;
            }

            $this->tryUpdateDesign($design, $postType, $getFieldsInstance);
        }

        $this->createInlineCssString();
        $this->wpService->updateOption($this->optionName, $this->designOption);
    }

    /**
     * Creates an inline CSS string based on the design options.
     *
     * If the design options are empty, the method returns early.
     * The method iterates over each design option and appends the inline CSS to the 'inlineCss' property.
     *
     * @return void
     */
    private function createInlineCssString(): void
    {
        if (empty($this->designOption)) {
            return;
        }

        $this->designOption['inlineCss'] = '';
        foreach ($this->designOption as $design) {
            $this->designOption['inlineCss'] .= $design['inlineCss'] ?? '';
        }
    }


    /**
     * Try to update the option with the design.
     *
     * @param mixed $design
     * @param string $postType
     * @param GetFieldsInterface $getFieldsInstance
     */
    private function tryUpdateDesign(mixed $design, string $postType, GetFieldsInterface $getFieldsInstance): void
    {
        [$designConfig, $css] = $this->configFromPageId->get($design);

        $sanitizedDesignConfigInstance = new ConfigSanitizer($designConfig, $getFieldsInstance->getFieldKeys());
        $inlineCssInstance             = new InlineCssGenerator($designConfig, $getFieldsInstance->getFields());

        $sanitizedDesignConfig = $sanitizedDesignConfigInstance->sanitize();
        $inlineCssString       = $inlineCssInstance->generateCssString();

        if (!empty($sanitizedDesignConfig)) {
            $this->designOption[$postType] = [
                'design'    => $sanitizedDesignConfig,
                'css'       => $css,
                'designId'  => $design,
                'inlineCss' => ".s-post-type-{$postType} { {$inlineCssString} }"
            ];
        }
    }

    /**
     * Remove the option key if necessary.
     *
     * @param mixed $design
     * @param string $postType
     */
    private function maybeRemoveOptionKey(mixed $design, string $postType): void
    {
        if (empty($design) && isset($this->designOption[$postType])) {
            unset($this->designOption[$postType]);
        }
    }

    /**
     * Check if the design exists or already has a value.
     *
     * @param mixed $design
     * @param string $postType
     * @return bool
     */
    private function hasDesignOrAlreadySetValue(mixed $design, string $postType): bool
    {
        $shouldUpdate = $this->wpService->getThemeMod($postType . '_post_type_update_design');

        if ($shouldUpdate && !empty($design)) {
            return false;
        }

        return empty($design) ||
        (isset($this->designOption[$postType]) &&
        $this->designOption[$postType]['designId'] === $design);
    }
}
