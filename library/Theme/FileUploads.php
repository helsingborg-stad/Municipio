<?php

namespace Municipio\Theme;

class FileUploads
{
    public function __construct()
    {
        add_action('wp_handle_upload_prefilter', array($this, 'santitizeFileNames'));
        add_filter('mime_types', array($this, 'mimeTypes'));
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

    /**
     * Add allowed mime types
     * @param  array $mimes  Mime types
     * @return array         Modified mime types
     */
    public function mimeTypes($mimes)
    {
        // Images
        $mimes['ico'] = 'image/x-icon';

        // Video
        $mimes['webm'] = 'video/webm';
        $mimes['mp4'] = 'video/mp4';
        $mimes['ogg'] = 'video/ogg';

        return $mimes;
    }
}
