<?php

declare(strict_types=1);

namespace Municipio\ImagePreload\Header;

use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use ComponentLibrary\Integrations\Image\ImageInterface;
use ComponentLibrary\Integrations\Image\ImageResolverInterface;
use Modularity\Integrations\Component\ImageResolver as ModularityImageResolver;

/**
 * Resolves image contracts for hero modules.
 */
class HeroModuleImageContractResolver
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
     * Resolve the hero module background image.
     *
     * @param array<string, mixed> $fields ACF fields.
     * @return ImageInterface|null
     */
    public function resolve(array $fields): ?ImageInterface
    {
        if (($fields['mod_hero_background_type'] ?? 'image') !== 'image') {
            return null;
        }

        $image = $fields['mod_hero_background_image'] ?? null;
        $imageId = is_array($image) && is_numeric($image['id'] ?? null)
            ? (int) $image['id']
            : null;

        if (!is_int($imageId) || $imageId <= 0) {
            return null;
        }

        return ImageComponentContract::factory($imageId, self::PRELOAD_IMAGE_SIZE, $this->imageResolver);
    }
}
