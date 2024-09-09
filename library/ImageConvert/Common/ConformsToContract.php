<?php

namespace Municipio\ImageConvert\Common;

class ConformsToContract
{
    /**
     * Check if the image array conforms to the contract.
     * Must be an array with a file key.
     * 
     * @param array $imageArray Array containing the image data.
     * 
     * @return bool True if the array conforms to the contract, false otherwise.
     */
    public static function conformsToContract($imageArray): bool
    {
      // Check if the array has exactly 4 elements
      if (count($imageArray) !== 4) {
          return false;
      }

      // Check if the elements conform to the expected types
      [$string, $numeric1, $numeric2, $boolean] = $imageArray;

      return is_string($string) && is_numeric($numeric1) && is_numeric($numeric2) && is_bool($boolean);
    }

    /**
     * Check if the image array conforms to the intermediary contract.
     * 
     * @param array $imageArray Array containing the image data.
     * 
     * @return bool True if the array conforms to the intermediary contract, false otherwise.
     */
    public static function conformsToIntermidiaryContract($imageArray): bool
    {
        if (!isset($imageArray['file'])) {
            return false;
        }

        return true; 
    }
}