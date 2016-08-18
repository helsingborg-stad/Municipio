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

        // MS Office
        $mimes['doc'] = 'application/msword';
        $mimes['pot|pps|ppt'] = 'application/vnd.ms-powerpoint';
        $mimes['wri'] = 'application/vnd.ms-write';
        $mimes['xla|xls|xlt|xlw'] = 'application/vnd.ms-excel';
        $mimes['mdb'] = 'application/vnd.ms-access';
        $mimes['mpp'] = 'application/vnd.ms-project';
        $mimes['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        $mimes['docm'] = 'application/vnd.ms-word.document.macroEnabled.12';
        $mimes['dotx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';
        $mimes['dotm'] = 'application/vnd.ms-word.template.macroEnabled.12';
        $mimes['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $mimes['xlsm'] = 'application/vnd.ms-excel.sheet.macroEnabled.12';
        $mimes['xlsb'] = 'application/vnd.ms-excel.sheet.binary.macroEnabled.12';
        $mimes['xltx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
        $mimes['xltm'] = 'application/vnd.ms-excel.template.macroEnabled.12';
        $mimes['xlam'] = 'application/vnd.ms-excel.addin.macroEnabled.12';
        $mimes['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
        $mimes['pptm'] = 'application/vnd.ms-powerpoint.presentation.macroEnabled.12';
        $mimes['ppsx'] = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
        $mimes['ppsm'] = 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12';
        $mimes['potx'] = 'application/vnd.openxmlformats-officedocument.presentationml.template';
        $mimes['potm'] = 'application/vnd.ms-powerpoint.template.macroEnabled.12';
        $mimes['ppam'] = 'application/vnd.ms-powerpoint.addin.macroEnabled.12';
        $mimes['sldx'] = 'application/vnd.openxmlformats-officedocument.presentationml.slide';
        $mimes['sldm'] = 'application/vnd.ms-powerpoint.slide.macroEnabled.12';
        $mimes['onetoc|onetoc2|onetmp|onepkg'] = 'application/onenote';

        // Drawings
        $mimes['dwg']  = 'application/dwg';

        return $mimes;
    }
}
