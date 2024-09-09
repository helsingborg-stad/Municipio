<?php 

namespace Municipio\ImageConvert\Common;

class IsconsideredImage
{
    public static function isConsideredImage($file): bool
    {
//wp_attachment_is_image



        $consideredExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        if (!isset($file['ext'])) {
            return false;
        }

        return in_array($file['ext'], $consideredExtensions);
    }
}