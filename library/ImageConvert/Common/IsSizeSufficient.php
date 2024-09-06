<?php

namespace Municipio\ImageConvert\Common;

class IsSizeSufficient
{
  /**
   * Check if the size is considered sufficient.
   * Must be an array with 2 numeric values greater than zero.
   *
   * @param array $size Array containing width and height values.
   * 
   * @return bool True if both width and height are numeric and greater than zero, false otherwise.
   */
  public static function isSizeSufficient(array $size): bool
  {
    if (count($size) !== 2) {
        return false;
    }
    $filteredSize = array_filter($size, fn($value) => is_numeric($value) && $value > 0);
    
    return count($filteredSize) === 2;
  }
}