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
        if (!$this->attachmentIsWOFF($id)) {
            return false;
        }

        return \Municipio\Helper\FileConverters\WoffConverter::convert($id);
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
