<?php

/**
 * ImageConvertFilter
 *
 * This class is responsible for filtering the image_downsize hook. Applying the image conversion flow.
 * This functionality will be used to resize and convert images to the requested size and format.
 * it will effectivly result in not having intermidiate sizes served and elliminates the need for
 * image regeneration plugins.
 *
 * Note: At least one of the dimensions must be an integer value!
 *
 * Usage: wp_get_attachment_image($id, [(int|bool) $width, (int|bool) $height]);
 *
 * - By sending a false value for width or height the image will
 *   be resized to the requested dimension keeping the aspect ratio.
 *
 * - By sending an integer value for width and height the image will
 *   be resized to the requested dimension without keeping the aspect ratio.
 *
 */

namespace Municipio\ImageConvert;

use Municipio\HooksRegistrar\Hookable;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\AddFilter;
use Municipio\ImageConvert\Contract\ImageContract;
use WpService\Contracts\GetPostMimeType;
use WpService\Contracts\WpGetAttachmentUrl;
use WpService\Contracts\IsAdmin;
use WpService\Contracts\__;
use WpService\Contracts\SizeFormat;

class ImageConvertFilter implements Hookable
{
    public function __construct(private AddFilter&ApplyFilters&WpGetAttachmentUrl&GetPostMimeType&IsAdmin&SizeFormat&__ $wpService, private ImageConvertConfig $config)
    {
    }

    public function addHooks(): void
    {
      //Max upload image size
        $this->wpService->addFilter(
            'wp_handle_upload_prefilter',
            [$this, 'preventLargeImageUploads'],
            5
        );

      //Only enable downsize filter for non admin users.
        if ($this->wpService->isAdmin()) {
            return;
        }

      //Image quality settings
        $this->wpService->addFilter(
            'wp_editor_set_quality',
            [$this, 'setImageQuality'],
            10,
            2
        );

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
        if (!is_array($size)) {
            return false;
        }

      //Check that it is a valid image size array.
        if (count($size) != 2) {
            return false;
        }

      //Detect that the array contains at least one integer value.
        if (!is_int($size[0]) && !is_int($size[1])) {
            return false;
        }

        // Check if the mime type is supported.
        if (!in_array($this->wpService->getPostMimeType($id), $this->config->mimeTypes())) {
            return false;
        }

        return $this->wpService->applyFilters(
            $this->config->createFilterKey(__FUNCTION__),
            ImageContract::factory(
                $this->wpService,
                $id,
                $size[0] ?? null,
                $size[1] ?? null
            )
        );
    }

  /**
   * Set image quality for the image editor.
   *
   * @param int $quality
   * @param string $mimeType
   *
   * @return int
   */
    public function setImageQuality($quality, $mimeType): int
    {
        return $this->config->intermidiateImageQuality();
    }

    /**
     * Prevent large image uploads from users.
     *
     * @param array $file
     *
     * @return array
     */
    public function preventLargeImageUploads($file): array
    {
        $limit    = $this->config->maxSourceFileSize();
        $is_image = strpos($file['type'], 'image') !== false;
        if ($is_image && $file['size'] > $limit) {
            $file['error'] = sprintf(
                $this->wpService->__("Image files must be smaller than %s", 'municipio'),
                $this->wpService->sizeFormat($limit, 1)
            );
        }
        return $file;
    }
}
