<?php

namespace Municipio\ImageConvert\Config;

use WpService\Contracts\ApplyFilters;
use Municipio\ImageConvert\Config\ImageConvertConfigInterface;

class ImageConvertConfig implements ImageConvertConfigInterface
{
    const FILTER_PREFIX              = 'Municipio/ImageConvert';
    const INTERMIDIATE_IMAGE_FORMAT  = 'webp';
    const INTERMIDIATE_IMAGE_QUALITY = 70;

    public function __construct(private ApplyFilters $wpService)
    {
    }

  /**
   * If the image conversion is enabled.
   */
    public function isEnabled(): bool
    {
        return $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            true
        );
    }

  /**
   * The maximum image dimension for image conversion.
   */
    public function maxImageDimension(): int
    {
        return $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            1920
        );
    }

  /**
   * The format to convert the intermidiate image to.
   *
   * @return string
   */
    public function intermidiateImageFormat(): array
    {
        $targetFormat = $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            self::INTERMIDIATE_IMAGE_FORMAT
        );

        if (!in_array($targetFormat, $this->fileNameSuffixes())) {
            throw new \Exception('Invalid target format');
        }

        //Alias jpg to jpeg
        $targetMime = ($targetFormat === 'jpg') ? 'jpeg' : $targetFormat;

        return [
        'suffix' => $targetFormat,
        'mime'   => 'image/' . $targetMime,
        ];
    }

    public function intermidiateImageQuality(): int
    {
        return $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            self::INTERMIDIATE_IMAGE_QUALITY
        );
    }

  /**
   * The priority to run ImageConvert on.
   */
    public function imageDownsizePriority(): int
    {
        return $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            1
        );
    }

  /**
   * The mime types that should be considered for image conversion.
   */
    public function mimeTypes(): array
    {
        return $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/tiff',
            'image/webp',
            ]
        );
    }

  /**
   * The suffixes for the mime types.
   * To filter this list, please use the mimeTypes filter.
   *
   * @return array
   */
    public function fileNameSuffixes(): array
    {
        $mimeTypes = $this->mimeTypes();

        return array_map(function ($mime) {
            return str_replace('image/', '', $mime);
        }, $mimeTypes);

      //Add jpg as an alias for jpeg
        if (in_array('jpeg', $mimeTypes)) {
            $mimeTypes[] = 'jpg';
        }

        return $mimeTypes;
    }

  /**
   * The internal filter priority for image conversion.
   *
   * This is the prority that the internal filters will hook into.
   *
   * @return object
   */
    public function internalFilterPriority(): object
    {
        return (object) $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            [
            'normalizeImageSize'       => 10,
            'resolveMissingImageSize'  => 20,
            'intermidiateImageConvert' => 30,
            'resolveToWpImageContract' => 40,
            ]
        );
    }

  /**
   * Create a prefix for image conversion filter.
   *
   * @return string
   */
    public function createFilterKey(string $filter = ""): string
    {
        return self::FILTER_PREFIX . "/" . ucfirst($filter);
    }
}
