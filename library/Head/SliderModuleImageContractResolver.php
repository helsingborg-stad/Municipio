<?php

declare(strict_types=1);

namespace Municipio\Head;

use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use ComponentLibrary\Integrations\Image\ImageInterface;
use ComponentLibrary\Integrations\Image\ImageResolverInterface;
use Modularity\Integrations\Component\ImageResolver as ModularityImageResolver;

/**
 * Resolves image contracts for slider modules.
 */
class SliderModuleImageContractResolver
{
    private const PRELOAD_IMAGE_SIZE = [1920, false];

    /**
     * @param ImageResolverInterface|null $imageResolver Image resolver for image contracts.
     */
    public function __construct(private ?ImageResolverInterface $imageResolver = null)
    {
        $this->imageResolver ??= new ModularityImageResolver();
    }

    /**
     * Resolve the first preloadable slider image.
     *
     * @param array<string, mixed> $fields ACF fields.
     * @return ImageInterface|null
     */
    public function resolve(array $fields): ?ImageInterface
    {
        $slides = $fields['slides'] ?? [];

        if (!is_array($slides)) {
            return null;
        }

        foreach ($slides as $slide) {
            if (!is_array($slide)) {
                continue;
            }

            $image = $slide['image'] ?? null;
            $imageId = match (true) {
                is_numeric($image) => (int) $image,
                is_array($image) && is_numeric($image['id'] ?? null) => (int) $image['id'],
                default => null,
            };

            if (!is_int($imageId) || $imageId <= 0) {
                continue;
            }

            return ImageComponentContract::factory($imageId, self::PRELOAD_IMAGE_SIZE, $this->imageResolver);
        }

        return null;
    }
}
