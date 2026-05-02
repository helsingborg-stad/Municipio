<?php

declare(strict_types=1);

namespace Municipio\Head;

use AcfService\AcfService;
use ComponentLibrary\Integrations\Image\ImageInterface;
use ComponentLibrary\Integrations\Image\ImageResolverInterface;

/**
 * Resolves image contracts for supported hero-area modules.
 */
class HeroImageContractResolver
{
    /**
     * @param AcfService $acfService ACF service wrapper.
     * @param ImageResolverInterface|null $imageResolver Image resolver for image contracts.
     */
    public function __construct(
        private AcfService $acfService,
        private ?ImageResolverInterface $imageResolver = null,
    ) {
    }

    /**
     * Resolve an image contract for a supported hero-area module.
     *
     * @param object $module Module object.
     * @return ImageInterface|null
     */
    public function resolve(object $module): ?ImageInterface
    {
        $moduleId = $module->ID ?? null;
        $moduleType = $module->post_type ?? null;

        if (!is_numeric($moduleId) || !is_string($moduleType)) {
            return null;
        }

        $fields = $this->acfService->getFields((int) $moduleId);

        if (!is_array($fields)) {
            return null;
        }

        return match ($moduleType) {
            'mod-hero' => (new HeroModuleImageContractResolver($this->imageResolver))->resolve($fields),
            'mod-slider' => (new SliderModuleImageContractResolver($this->imageResolver))->resolve($fields),
            default => null,
        };
    }
}
