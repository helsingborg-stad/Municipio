<?php

namespace Municipio\ImageConvert\Resolvers\MissingSize;

use Municipio\ImageConvert\Contract\ImageContract;
use WpService\Contracts\WpUpdateAttachmentMetadata;

/**
 * Resolve missing image size by fetching the image size from the file.
 */
class ResolveMissingImageSizeByFile implements ResolveMissingImageSizeInterface
{
    /**
     * Constructor.
     */
    public function __construct(private WpUpdateAttachmentMetadata $wpService, private ?ResolveMissingImageSizeInterface $inner = null)
    {
        $this->inner = $inner ?? new ResolveMissingImageSizeDefault();
    }

    /**
     * Get the dimensions of the image.
     * @param ImageContract $image
     * @return array|null
     */
    public function getAttachmentDimensions(ImageContract $image): ?array
    {
        // Get the file path from the ImageContract
        $file = $image->getPath();

        if ($file && $this->isConsideredImage($file)) {
            $fetchedImage = file_exists($file) ? getimagesize($file) : false;

            if ($fetchedImage !== false && isset($fetchedImage[0], $fetchedImage[1])) {
                $size = ['width' => $fetchedImage[0], 'height' => $fetchedImage[1]];

                if ($this->isSizeSufficient($size)) {
                    $this->wpService->wpUpdateAttachmentMetadata(
                        $image->getId(),
                        $size
                    );
                    return $size;
                }
            }
        }

        // Delegate to the inner resolver if size could not be determined
        return $this->inner->getAttachmentDimensions($image);
    }

    /**
     * Check if the file is considered an image by verifying the MIME type.
     *
     * @param string $file
     * @return bool
     */
    private function isConsideredImage(string $file): bool
    {
        $mimeType = mime_content_type($file);

        // Check if the MIME type starts with 'image/'
        return strpos($mimeType, 'image/') === 0;
    }

    /**
     * Check if the width and height of the image are set.
     *
     * @param array $size
     * @return bool
     */
    private function isSizeSufficient(array $size): bool
    {
        return isset($size['width'], $size['height']);
    }
}
