<?php

namespace Municipio\ImageConvert\Resolvers\MissingSize;

use Municipio\ImageConvert\Common\IsConsideredImage;
use Municipio\ImageConvert\Common\IsSizeSufficient;
use Municipio\ImageConvert\Contract\ImageContract;

class ResolveMissingImageSizeByFile implements ResolveMissingImageSizeInterface
{
    public function __construct(private $wpService, private ?ResolveMissingImageSizeInterface $inner = null)
    {
        $this->inner = $inner ?? new ResolveMissingImageSizeDefault();
    }

    public function getAttachmentDimensions(ImageContract $image): ?array
    {
        $file = get_attached_file($image->getId()); //$this->wpService->getAttachmedFile($id);
        if ($file && IsConsideredImage::isConsideredImage($file)) {
            $fetchedImage = file_exists($file) ? getimagesize($file) : false;
            if ($fetchedImage !== false && isset($fetchedImage[0], $fetchedImage[1])) {
                $size = ['width' => $fetchedImage[0], 'height' => $fetchedImage[1]];

                if (IsSizeSufficient::isSizeSufficient($size)) {
                    wp_update_attachment_metadata($image->getId(), $size);
                    return $size;
                }
            }
        }

        // Delegate to the inner resolver
        return $this->inner->getAttachmentDimensions($image);
    }
}