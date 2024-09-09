<?php

namespace Municipio\ImageConvert;

class ResolveMissingImageSizeByMeta implements ResolveMissingImageSizeInterface
{
  public function __construct(private $wpService, private ?ResolveMissingImageSizeInterface $inner = null)
  {
    $this->inner = $inner ?? new ResolveMissingImageSizeByFile(
      $wpService
    );
  }

  public function getAttachmentDimensions(int $id): ?array
  {
      $metaData = wp_get_attachment_metadata($id);  // $this->wpService->getAttachmentMetadata($id);
      if (!empty($metaData['width']) && !empty($metaData['height'])) {
          return [
              $metaData['width'],
              $metaData['height']
          ];
      }

      // Delegate to the inner resolver
      return $this->inner->getAttachmentDimensions($id);
  }
}