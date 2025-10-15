<?php

namespace Modularity\Integrations\Component;

use \ComponentLibrary\Integrations\Image\ImageFocusResolverInterface;

class ImageFocusResolver implements ImageFocusResolverInterface {

  /**
   * Constructor
   * 
   * @param array $data The data array to resolve from
   */
  public function __construct(private $data){}

  /**
   * Get focus point
   * 
   * @return array
   */
  public function getFocusPoint(): array {
    $data = $this->data;
    $focusPoint = [
      'left' => 50,
      'top' => 50
    ];
  
    if ($data && isset($data['left'], $data['top'])) {
      $focusPoint['left'] = $data['left'] ?? 50;
      $focusPoint['top'] = $data['top'] ?? 50;
    }

    if (!empty($data['id']) && $focusPoint['left'] === 50 && $focusPoint['top'] === 50) {
      $focusPoint = apply_filters('attachment_focus_point', $focusPoint, $data['id']);
    }

    return $focusPoint;
  } 
}
