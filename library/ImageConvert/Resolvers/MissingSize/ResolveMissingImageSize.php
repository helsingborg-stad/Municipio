<?php

namespace Municipio\ImageConvert\Resolvers\MissingSize;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\ImageConvert\Resolvers\MissingSize\ResolveMissingImageSizeInterface;
use Municipio\ImageConvert\Resolvers\MissingSize\ResolveMissingImageSizeByMeta;
use Municipio\ImageConvert\Contract\ImageContract;

class ResolveMissingImageSize implements Hookable
{
    private ResolveMissingImageSizeInterface $resolver;

    public function __construct(private $wpService, private ImageConvertConfig $config)
    {
        $this->wpService  = $wpService;
        $this->config     = $config;
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter(
            $this->config->createFilterKey('imageDownsize'),
            [$this, 'resolveMissingImageSize'],
            $this->config->internalFilterPriority()->resolveMissingImageSize,
            1
        );
    }

    /**
     * Inits and resolves missing image size from various sources.
     * 
     * @param ImageContract $image
     * 
     * @return ImageContract
     */
    public function resolveMissingImageSize($image): ImageContract|false
    {
        if(!$image instanceof ImageContract) {
            return $image;
        }

        //Initialize the resolver
        $resolver = new ResolveMissingImageSizeByMeta($this->wpService);
        $resolvedImageSize       = $resolver->getAttachmentDimensions($image);

        $resolvedImageSizeScaled = $this->calculateScaledDimensions(
            $image, 
            $resolvedImageSize
        );

        //Update image with resolved width
        if(is_numeric($resolvedImageSizeScaled['width'])) {
            $image->setWidth($resolvedImageSizeScaled['width']);
        }

        //Update image with resolved height
        if(is_numeric($resolvedImageSizeScaled['height'])) {
            $image->setHeight($resolvedImageSizeScaled['height']);
        }

        return $image;
    }

    /**
     * Scale resolved dimensions to match the aspect ratio of the original image.
     * 
     * @param ImageContract $image
     * @param array $resolvedImageSize
     * 
     * @return array
     */
    private function calculateScaledDimensions(ImageContract $image, array $resolvedImageSize): array
{
    // Get the original dimensions from the image contract
    $originalWidth  = $image->getWidth();
    $originalHeight = $image->getHeight();

    // Ensure the resolved dimensions are available and valid
    $resolvedWidth  = $resolvedImageSize['width'] ?? null;
    $resolvedHeight = $resolvedImageSize['height'] ?? null;

    // Check if both resolvedWidth and resolvedHeight are available
    if (!is_numeric($resolvedWidth) || !is_numeric($resolvedHeight)) {
        return $resolvedImageSize; // Fallback to resolved image size if invalid
    }

    // If one of the original dimensions is null, calculate it using the resolved dimensions
    if ($originalWidth === null && $originalHeight !== null) {
        // Calculate originalWidth based on aspect ratio and resolvedHeight
        $originalWidth = ($resolvedWidth / $resolvedHeight) * $originalHeight;
    } elseif ($originalHeight === null && $originalWidth !== null) {
        // Calculate originalHeight based on aspect ratio and resolvedWidth
        $originalHeight = ($resolvedHeight / $resolvedWidth) * $originalWidth;
    }

    // If both dimensions are missing, fallback to resolved dimensions
    if ($originalWidth === null || $originalHeight === null) {
        return $resolvedImageSize; // Can't scale, so return the resolved dimensions
    }

    // Return the scaled dimensions
    return [
        'width' => (int) round($originalWidth),
        'height' => (int) round($originalHeight),
    ];
}
}