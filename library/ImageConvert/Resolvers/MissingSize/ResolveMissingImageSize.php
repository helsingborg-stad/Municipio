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
        $resolvedImageSize       =  $resolver->getAttachmentDimensions($image);
        $resolvedImageSizeScaled = $this->calculateScaledDimensions($image, $resolvedImageSize);

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

    private function calculateScaledDimensions($image, $resolvedImageSize): array
    {
        $width  = $image->getWidth();
        $height = $image->getHeight();

        if ($width > $resolvedImageSize['width'] || $height > $resolvedImageSize['height']) {
            $ratio = $width / $height;

            if ($width > $height) {
                $width = $resolvedImageSize['width'];
                $height = $width / $ratio;
            } else {
                $height = $resolvedImageSize['height'];
                $width = $height * $ratio;
            }
        }

        return ['width' => $width, 'height' => $height];
    }
}