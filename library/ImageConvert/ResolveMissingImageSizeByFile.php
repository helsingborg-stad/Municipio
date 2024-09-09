<?php

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Common\IsConsideredImage;
use Municipio\ImageConvert\Common\IsSizeSufficient;

class ResolveMissingImageSizeByFile implements ResolveMissingImageSizeInterface
{
    public function __construct(private $wpService, private ?ResolveMissingImageSizeInterface $inner = null)
    {
        $this->inner = $inner ?? new ResolveMissingImageSizeDefault();
    }

    public function getAttachmentDimensions(int $id): ?array
    {
        $file = get_attached_file($id); //$this->wpService->getAttachmedFile($id);
        if ($file && IsConsideredImage::isConsideredImage($file)) {
            $fetchedImage = file_exists($file) ? getimagesize($file) : false;
            if ($fetchedImage !== false && isset($fetchedImage[0], $fetchedImage[1])) {
                $size = [$fetchedImage[0], $fetchedImage[1]];

                if (IsSizeSufficient::isSizeSufficient($size)) {
                    wp_update_attachment_metadata($id, [
                        'width' => $size[0],
                        'height' => $size[1]
                    ]);

                    /*$this->wpService->updateAttachmentMetadata($id, [
                        'width' => $size[0],
                        'height' => $size[1]
                    ]);*/ 
                    return $size;
                }
            }
        }

        // Delegate to the inner resolver
        return $this->inner->getAttachmentDimensions($id);
    }
}