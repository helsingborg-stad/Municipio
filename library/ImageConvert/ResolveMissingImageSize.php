<?php

namespace Municipio\ImageConvert;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;

class ResolveMissingImageSize implements Hookable
{
    private $wpService;
    private ImageConvertConfig $config;
    private ResolveMissingImageSizeInterface $resolver;

    public function __construct($wpService, ImageConvertConfig $config)
    {
        $this->wpService  = $wpService;
        $this->config     = $config;

        $this->resolver = new ResolveMissingImageSizeByMeta($wpService);
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter(
            $this->config->createFilterKey('imageDownsize'),
            [$this, 'resolveMissingImageSize'],
            $this->config->internalFilterPriority()->resolveMissingImageSize,
            3
        );
    }

    public function resolveMissingImageSize($false, $id, $size): mixed
    { 


      var_dump($size);


      if(!$this->isSpecificImageSize($size)) {
        return $size;
      }


      // Use the resolver chain to find the image dimensions
      $sourceFileSize = $this->resolver->getAttachmentDimensions($id);

      if ($sourceFileSize !== null) {
          $size = $this->calculateRelativeSize($size, $sourceFileSize);
      }

      return $size;
    }

    private function calculateRelativeSize(array $size, array $sizeFile): array
    {
        [$width, $height] = $size;
        [$fileWidth, $fileHeight] = $sizeFile;

        if ($width === false) {
            $width = $height * ($fileWidth / $fileHeight);
        }

        if ($height === false) {
            $height = $width * ($fileHeight / $fileWidth);
        }

        return [$width, $height];
    }

    /**
     * Check if the size is a specific image size.
     * This is a check to see if the size should be processsed.
     */
    private function isSpecificImageSize(mixed $size): bool
    {
        return is_array($size) && count($size) === 2;
    }
}