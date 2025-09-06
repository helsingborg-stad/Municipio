<?php

namespace Municipio\ImageConvert\Config;

use WpService\Contracts\ApplyFilters;
use Municipio\ImageConvert\Config\ImageConvertConfigInterface;

/**
 * ImageConvert configuration.
 */
class ImageConvertConfig implements ImageConvertConfigInterface
{
    /**
     * Constructor.
     *
     * @param ApplyFilters $wpService               A wp service instance.
     * @param string $filterPrefix                  The filter prefix for the image conversion filters.
     * @param string $intermidiateImageFormat       The format to convert the intermidiate image to.
     * @param int $intermidiateImageQuality         The quality of the intermidiate image.
     * @param int $intermidiateImageMaxDimension    The maximum image dimension for image conversion.
     * @param int $maxSourceFileSize                The maximum file size for the source image.
     */
    public function __construct(
        private ApplyFilters $wpService,
        private string $filterPrefix = 'Municipio/ImageConvert',
        private string $intermidiateImageFormat = 'webp',
        private int $intermidiateImageQuality = 80,
        private int $intermidiateImageMaxDimension = 1920,
        private int $maxSourceFileSize = 5
    ) {
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
            $this->intermidiateImageMaxDimension
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
            $this->intermidiateImageFormat
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

    /**
     * The quality of the intermidiate image.
     */
    public function intermidiateImageQuality(): int
    {
        return $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            $this->intermidiateImageQuality
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
        $originalMimeList = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/tiff',
            'image/webp',
        ];
        $mime             = $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            $originalMimeList
        );
        if (is_array($mime)) {
            return $mime;
        }
        return $originalMimeList;
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

        //Scrub to file extensions
        $mimeTypes = array_map(function ($mime) {
            return str_replace('image/', '', $mime);
        }, $mimeTypes);

        //Add jpg as an alias for jpeg
        if (in_array('jpeg', $mimeTypes)) {
            $mimeTypes[] = 'jpg';
        }

        return $mimeTypes;
    }

    /**
     * The maximum file size for the source image.
     * This will be used to determine if the image should be converted or not.
     *
     * The default value is 20MB.
     *
     * @return int The maximum file size in bytes.
     */
    public function maxSourceFileSize(): int
    {
        return $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            1024 * 1024 * $this->maxSourceFileSize
        );
    }

    /**
     * This is the time to use to consider a post recently touched.
     * This is used when using the mixed strategy to determine if the image should 
     * be converted immediately or passed for background processing.
     *
     * @return int Time limit in seconds.
     */
    public function mixedStrategyEditorTimeframeSeconds(): int
    {
        return $this->wpService->applyFilters(
            $this->createFilterKey(__FUNCTION__),
            3600
        );
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
        return $this->filterPrefix . "/" . ucfirst($filter);
    }
}
