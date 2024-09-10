<?php

namespace Municipio\ImageConvert\Resolvers\MissingSize;

use Municipio\ImageConvert\Contract\ImageContract;
class ResolveMissingImageSizeByMeta implements ResolveMissingImageSizeInterface
{
  public function __construct(private $wpService, private ?ResolveMissingImageSizeInterface $inner = null)
  {
    $this->inner = $inner ?? new ResolveMissingImageSizeByFile(
      $wpService
    );
  }

  public function getAttachmentDimensions(ImageContract $image): ?array
  {
      $metaData = wp_get_attachment_metadata($image->getId());

      if (!empty($metaData['width']) && !empty($metaData['height'])) {
          return [
              'width' => $metaData['width'],
              'height' => $metaData['height']
          ];
      }

      // Delegate to the inner resolver
      //return $this->inner->getAttachmentDimensions($image);
  }
}