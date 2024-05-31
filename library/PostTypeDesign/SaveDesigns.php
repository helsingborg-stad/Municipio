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
    /**
     * SaveDesigns constructor.
     *
     * @param string $optionName
     * @param AddAction&GetOption&GetThemeMod&GetPostTypes&UpdateOption $wpService
     * @param ConfigFromPageIdInterface $configFromPageId
     */
    public function __construct(
        private string $optionName,
        private AddAction&GetOption&GetThemeMod&GetPostTypes&UpdateOption $wpService,
        private ConfigFromPageIdInterface $configFromPageId
    ) {
    }

    /**
     * Add hooks for saving designs.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('customize_save_after', array($this, 'storeDesigns'));
        // $this->wpService->addAction('wp', array($this, 'storeDesigns'));
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

        // $designOption = $this->wpService->getOption($this->optionName);
        $designOption      = [];
        $getFieldsInstance = new GetFields(PanelsRegistry::getInstance()->getRegisteredFields());

        foreach ($postTypes as $postType) {
            $design = $this->wpService->getThemeMod($postType . '_load_design');

            if ($this->hasDesignOrAlreadySetValue($design, $designOption, $postType)) {
                $this->maybeRemoveOptionKey($design, $designOption, $postType);
                continue;
            }

            $this->tryUpdateOptionWithDesign($design, $designOption, $postType, $getFieldsInstance);
        }
    }

    /**
     * Try to update the option with the design.
     *
     * @param mixed $design
     * @param mixed $designOption
     * @param string $postType
     */
    public function tryUpdateOptionWithDesign(
        mixed $design,
        mixed $designOption,
        string $postType,
        GetFieldsInterface $getFieldsInstance
    ): void {
        [$designConfig, $css] = $this->configFromPageId->get($design);

        $sanitizedDesignConfigInstance = new ConfigSanitizer($designConfig, $getFieldsInstance->getFieldKeys());
        $inlineCssInstance             = new InlineCssGenerator($designConfig, $getFieldsInstance->getFields());

        $sanitizedDesignConfig = $sanitizedDesignConfigInstance->sanitize();
        $inlineCss             = $inlineCssInstance->generate();
        if (!empty($sanitizedDesignConfig)) {
            $designOption[$postType] = [
                'design'    => $sanitizedDesignConfig,
                'css'       => $css,
                'designId'  => $design,
                'inlineCss' => $inlineCss
            ];

            $this->wpService->updateOption($this->optionName, $designOption);
        }
    }

    /**
     * Remove the option key if necessary.
     *
     * @param mixed $design
     * @param mixed $designOption
     * @param string $postType
     */
    private function maybeRemoveOptionKey(mixed $design, mixed $designOption, string $postType): void
    {
        if (empty($design) && isset($designOption[$postType])) {
            unset($designOption[$postType]);
            $this->wpService->updateOption($this->optionName, $designOption);
        }
    }

    /**
     * Check if the design exists or already has a value.
     *
     * @param mixed $design
     * @param mixed $designOption
     * @param string $postType
     * @return bool
     */
    private function hasDesignOrAlreadySetValue(mixed $design, mixed $designOption, string $postType): bool
    {
        $shouldUpdate = $this->wpService->getThemeMod($postType . '_post_type_update_design');

        if ($shouldUpdate && !empty($design)) {
            return false;
        }

        return empty($design) ||
            (isset($designOption[$postType]) &&
            $designOption[$postType]['designId'] === $design);
    }
}
