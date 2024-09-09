<?php 

namespace Municipio\ImageConvert;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\AddFilter;

class ImageConvertFilter implements Hookable
{
  public function __construct(private AddFilter&ApplyFilters $wpService, private ImageConvertConfig $config){}

  public function addHooks(): void
  {
    $this->wpService->addFilter(
      'image_downsize', 
      [$this, 'imageDownsize'], 
      $this->config->imageDownsizePriority(),
      3 //Number of arguments to pass to the callback function.
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
    return $this->wpService->applyFilters(
      $this->config->createFilterKey(__FUNCTION__),
      $false,
      $id,
      $size
    );
  }
}