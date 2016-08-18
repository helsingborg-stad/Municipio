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
        $file['name']   = $this->localLetterSanitize(sanitize_title($new_filename)) . '.' . $path['extension'];
        return $file;
    }

    /**
     * Sanitize local letters from string
     * @param  string $name  Unzanitized filename
     * @return string new santized filename
     */
    public function localLetterSanitize($name)
    {
        $search = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');

        return str_replace($search, $replace, $name);
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
