<?php

namespace Municipio\Theme;

class FileUploads
{
    public function __construct()
    {
        add_action('wp_handle_upload_prefilter', array($this, 'santitizeFileNames'));
    }

    /**
     * Santitize file names
     * @param  array $file with parameters for uploaded file
     * @return string      Array with file specifications
     */
    public function santitizeFileNames($file)
    {
        $path           = pathinfo($file['name']);
        $new_filename   = preg_replace('/.' . $path['extension'] . '$/', '', $file['name']);
        $file['name']   = sanitize_title($new_filename) . '.' . $path['extension'];
        return $file;
    }
}
