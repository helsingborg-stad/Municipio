<?php 

namespace Municipio\ImageConvert;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\AddFilter;
use Municipio\ImageConvert\Contract\ImageContract;

class ImageConvertFilter implements Hookable
{
  public function __construct(private AddFilter&ApplyFilters $wpService, private ImageConvertConfig $config){}

  public function addHooks(): void
  {
    $this->wpService->addFilter(
      'image_downsize', 
      [$this, 'imageDownsize'], 
      $this->config->imageDownsizePriority(),
      3
    );
  }

  /**
   * Creates an internal filter for image conversion.
   * This effectivly garatuees that the image conversion 
   * flow is in a chronological order without sideeffects 
   * unless requested by injected filter between prioritys.
   */
  public function imageDownsize($false, $id, $size): mixed
  {
    //Check if the requested size is something we can handle.
    if(!is_array($size) && count($size) === 2) {
      return false;
    }
    return $this->wpService->applyFilters(
      $this->config->createFilterKey(__FUNCTION__),
      ImageContract::factory($id, $size[0] ?? null, $size[1] ?? null)
    );
  }
}