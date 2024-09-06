<?php

namespace Municipio\ImageConvert\Common;

class IsSpecificImageSize
{
  /**
   * Check if the image size is specific.
   * Needs to be an array with 1 or 2 values.
   */
  public static function isSpecificImageSize($size): bool
  {
    return is_array($size) && in_array(count($size), [1, 2]);
  }
}