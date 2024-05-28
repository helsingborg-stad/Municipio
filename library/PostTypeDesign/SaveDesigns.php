<?php

namespace Municipio\PostTypeDesign;

use Municipio\PostTypeDesign\ConfigSanitizer;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\UpdateOption;

class SaveDesigns
{
    public function __construct(
        private string $optionName,
        private AddAction&GetOption&GetThemeMod&GetPostTypes&UpdateOption $wpService,
        private ConfigFromPageIdInterface $configFromPageId
    ) {
        $this->addHooks();
    }

    public function addHooks()
    {
        $this->wpService->addAction('customize_save_after', array($this, 'storeDesigns'));
        // $this->wpService->addAction('wp', array($this, 'storeDesigns'));
    }

    public function storeDesigns(): void
    {
        $postTypes = $this->wpService->getPostTypes(['public' => true], 'names');
        if (empty($postTypes)) {
            return;
        }

        $designOption = $this->wpService->getOption('post_type_design');

        foreach ($postTypes as $postType) {
            $design = $this->wpService->getThemeMod($postType . '_load_design');

            if ($this->hasDesignOrAlreadySetValue($design, $designOption, $postType)) {
                $this->maybeRemoveOptionKey($design, $designOption, $postType);
                continue;
            }

            $this->tryUpdateOptionWithDesign($design, $designOption, $postType);
        }
    }

    public function tryUpdateOptionWithDesign(mixed $design, mixed $designOption, string $postType): void
    {
        [$designConfig, $css] = $this->configFromPageId->get($design);

        $sanitizedDesignConfig = $this->getDesignConfig($designConfig);

        if (!empty($sanitizedDesignConfig)) {
            $designOption[$postType] = [
                'design'   => $sanitizedDesignConfig,
                'css'      => $css,
                'designId' => $design
            ];

            $this->wpService->updateOption('post_type_design', $designOption);
        }
    }

    private function maybeRemoveOptionKey(mixed $design, mixed $designOption, string $postType): void
    {
        if (empty($design) && isset($designOption[$postType])) {
            unset($designOption[$postType]);
            $this->wpService->updateOption('post_type_design', $designOption);
        }
    }

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

    private function getDesignConfig(array $designConfig): array
    {
        $keys = array_merge(MultiColorKeys::get(), ColorKeys::get(), BackgroundKeys::get());

        $configTransformerInstance = new ConfigSanitizer(
            $designConfig,
            $keys
        );

        return $configTransformerInstance->transform();
    }
}
