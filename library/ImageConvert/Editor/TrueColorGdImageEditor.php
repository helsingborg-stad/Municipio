<?php

namespace Municipio\ImageConvert\Editor;

/**
 * GD image editor that converts palette images to true colour before saving.
 *
 * GD cannot write palette images as WebP ("Palette image not supported by webp").
 * WordPress only creates a true colour copy when it actually resizes, so palette
 * PNGs and GIFs converted at their original size would fail.
 */
class TrueColorGdImageEditor extends \WP_Image_Editor_GD
{
    /**
     * @inheritDoc
     */
    protected function _save($image, $filename = null, $mime_type = null)
    {
        if ($image instanceof \GdImage && !imageistruecolor($image)) {
            imagepalettetotruecolor($image);
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        return parent::_save($image, $filename, $mime_type);
    }
}
