<?php

declare(strict_types=1);

namespace Municipio\Head;

use ComponentLibrary\Integrations\Image\ImageInterface;

/**
 * Resolves preload attributes for the first eligible image in the hero sidebar.
 */
class HeroImagePreloadResolver
{
    /**
     * @param HeroImageModuleProvider $moduleProvider Hero module provider.
     * @param HeroImageContractResolver|null $imageContractResolver Hero image contract resolver.
     */
    public function __construct(
        private HeroImageModuleProvider $moduleProvider,
        private ?HeroImageContractResolver $imageContractResolver = null,
    ) {
    }

    /**
     * Resolve preload link attributes for the first eligible hero image.
     *
     * @return array<string, string>|null
     */
    public function resolve(): ?array
    {
        foreach ($this->moduleProvider->get() as $module) {
            $image = $this->getImageContractResolver()->resolve($module);

            if ($image === null) {
                continue;
            }

            return $this->getPreloadAttributes($image);
        }

        return null;
    }

    /**
     * Get the hero image contract resolver.
     */
    private function getImageContractResolver(): HeroImageContractResolver
    {
        if ($this->imageContractResolver === null) {
            throw new \RuntimeException('HeroImageContractResolver must be provided.');
        }

        return $this->imageContractResolver;
    }

    /**
     * Convert an image contract to preload attributes.
     *
     * @param ImageInterface $image Image contract.
     * @return array<string, string>|null
     */
    private function getPreloadAttributes(ImageInterface $image): ?array
    {
        $href = $image->getUrl();

        if (!is_string($href) || $href === '') {
            return null;
        }

        return array_filter([
            'href' => $href,
            'imagesrcset' => $image->getSrcSet(),
            'imagesizes' => '100vw',
            'fetchpriority' => 'high',
        ], static fn(mixed $value): bool => is_string($value) && $value !== '');
    }
}
