<?php

namespace Municipio\Admin;

/**
 * Class Uploads
*/
class Uploads
{
    /**
     * Construct
    */
    public function addHooks()
    {
        add_action('add_attachment', array($this, 'convertWOFFToTTF'));
    }

    /**
     * Construct
    */
    public function convertWOFFToTTF(int $id)
    {
        if ($this->attachmentIsWOFF($id)) {
            $convertedUrl = \Municipio\Helper\FileConverters\WoffConverter::convert($id);

            if ($convertedUrl) {
                add_post_meta($id, 'ttf', $convertedUrl);

                return $convertedUrl;
            }
        }

        return false;
    }

    /**
     * Construct
    */
    private function attachmentIsWOFF(int $id)
    {
        $mimeType = get_post_mime_type($id);

        return $mimeType === 'application/font-woff' ? true : false;
    }
}
