<?php

namespace Municipio\ImageConvert\Resolvers\MissingSize;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\ImageConvert\Resolvers\MissingSize\ResolveMissingImageSizeByMeta;
use Municipio\ImageConvert\Contract\ImageContract;
use WpService\Contracts\IsAdmin;
use WpService\Contracts\AddFilter;
use WpService\Contracts\WpGetAttachmentMetadata;

class ResolveMissingImageSize implements Hookable
{
    public function __construct(private IsAdmin&AddFilter&WpGetAttachmentMetadata $wpService, private ImageConvertConfig $config)
    {
        $this->wpService = $wpService;
        $this->config    = $config;
    }

    public function addHooks(): void
    {
        if ($this->wpService->isAdmin()) {
            return;
        }

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
        if (!$image instanceof ImageContract) {
            return $image;
        }

        //Bypass if both width and height is set
        if (is_numeric($image->getWidth()) && is_numeric($image->getHeight())) {
            return $image;
        }

        //Initialize the resolver
        $resolver          = new ResolveMissingImageSizeByMeta($this->wpService);
        $resolvedImageSize = $resolver->getAttachmentDimensions($image);

        //Calculate scaled dimensions
        $resolvedImageSizeScaled = $this->calculateScaledDimensions(
            $image,
            $resolvedImageSize
        );

        //Update image with resolved width
        if (is_numeric($resolvedImageSizeScaled['width'])) {
            $image->setWidth($resolvedImageSizeScaled['width']);
        }

        //Update image with resolved height
        if (is_numeric($resolvedImageSizeScaled['height'])) {
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
    public function calculateScaledDimensions(ImageContract $image, array $resolvedImageSize): array
    {
        //Get image dimensions requested
        $imageWidth  = $image->getWidth() ?: null;
        $imageHeight = $image->getHeight() ?: null;

        // If width is not set, calculate it based on the aspect ratio
        if (!is_numeric($imageWidth)) {
            $imageWidth = round(($imageHeight / $resolvedImageSize['height']) * $resolvedImageSize['width']);
        }

        // If height is not set, calculate it based on the aspect ratio
        if (!is_numeric($imageHeight)) {
            $imageHeight = round(($imageWidth / $resolvedImageSize['width']) * $resolvedImageSize['height']);
        }

        // Return dimensions with calculated values
        return [
            'width'  => (int) ($imageWidth),
            'height' => (int) ($imageHeight),
        ];
    }
}
