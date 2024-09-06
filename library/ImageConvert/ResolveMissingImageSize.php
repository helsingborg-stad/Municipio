<?php

namespace Municipio\ImageConvert;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\ImageConvert\Common\IsSpecificImageSize;
use Municipio\ImageConvert\Common\IsSizeSufficient;

class ResolveMissingImageSize implements Hookable
{
  public function __construct(private $wpService, private ImageConvertConfig $config){}

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

    if(!IsSpecificImageSize::isSpecificImageSize($size)) {
      return $size;
    }

    if(IsSizeSufficient::isSizeSufficient($size)) {
      return $size;
    }

    //Use this first to avoid file access if possible.
    $sizeMeta = ($this->getAttachmentMetaDataDimensions($id));

    //If the metadata is not sufficient, fetch the image file.
    $sizeFile = ($this->getAttachmentFileDimensions($id));

    //Calculate a new relative size for missing size values.
    $size = $this->calculateRelativeSize($size, $sizeMeta, $sizeFile);
    
    

    return $size;
  }

  private function calculateRelativeSize($size, $sizeMeta, $sizeFile): array
  {
    if($size[0] === false) {
      $size[0] = $size[1] * ($sizeFile[0] / $sizeFile[1]);
    }

    if($size[1] === false) {
      $size[1] = $size[0] * ($sizeFile[1] / $sizeFile[0]);
    }

    return $size;
  }

  private function getAttachmentMetaDataDimensions($id): ?array
  {
    $metaData = wp_get_attachment_metadata($id); //TODO: Implement in wpservice
    if(!empty($metaData['width']) && !empty($metaData['height'])) {
      return [
        $metaData['width'],
        $metaData['height']
      ];
    }
    return null;
  }

  private function getAttachmentFileDimensions($id): array
  {
    if($file = get_attached_file($id)) { //TODO: Implement in wpservice

      if(IsConsideredImage::isConsideredImage($file)) {
        return $this->getImageDimensions($file, $id);
      }


      $fetchedImage = getimagesize($file);
      if($fetchedImage !== false) {
        $size = [
          $fetchedImage[0],
          $fetchedImage[1]
        ];
      }
    }
    
    //Check if valid
    if(!isset($size) || !IsSizeSufficient::isSizeSufficient($size)) {
      return $this->config->defaultImageDimensions();
    }

    //Store the dimensions in the attachment metadata. 
    //This will avoid a future file access. 
    $this->upsertAttachmentMetaData($id, $size);

    return [
      $size[0],
      $size[1]
    ];
  }

  /**
   * Update or inser the attachment size metadata.
   */
  private function upsertAttachmentMetaData($id, $size): bool
  {
    $result = wp_update_attachment_metadata($id, [ //TODO: Implement in wpservice
      'width' => $size[0],
      'height' => $size[1],
    ]);
    return (bool) $result;
  }
}