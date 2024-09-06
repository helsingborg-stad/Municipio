<?php 

namespace Municipio\ImageConvert;

class IsconsideredImage
{
    public function isConsideredImage($file): bool
    {
        $consideredExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        if (!isset($file['ext'])) {
            return false;
        }

        return in_array($file['ext'], $consideredExtensions);
    }
}