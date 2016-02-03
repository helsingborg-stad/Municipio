<?php

namespace Municipio\Theme;

class OnTheFlyImages
{
    public function __construct()
    {
        add_filter('image_downsize', array($this,'runResizeImage'), 5, 3 );
    }

    /* Hook to image resize function
     *
     * @param  int     $downsize      Always false (do not try to find an alternative).
     * @param  int     $id            Image ID
     * @param  int     $size          An ARRAY($width, $height)
     *
     * @return string|bool            URL of resized image, false if error
     */

    public function runResizeImage($downsize, $id, $size)
    {
        if (is_array($size)) {
            return array(
                $this->resizeImage($id, $size[0], $size[1], true),
                $size[0],
                $size[1],
                true
            );
        } else {
            return false;
        }
    }

    /* Resize image on the fly
     *
     * @param  int     $attachment_id Attachment ID
     * @param  int     $width         Width
     * @param  int     $height        Height
     * @param  boolean $crop          Crop or not
     *
     * @return string|bool            URL of resized image, false if error
     */

    public function resizeImage($attachment_id, $width, $height, $crop = true)
    {
        // Get upload directory info
        $upload_info = wp_upload_dir();
        $upload_dir  = $upload_info['basedir'];
        $upload_url  = $upload_info['baseurl'];

        // Get file path info
        $path      = get_attached_file($attachment_id);
        $path_info = pathinfo($path);
        $ext       = $path_info['extension'];
        $rel_path  = str_replace(array( $upload_dir, ".$ext" ), '', $path);
        $suffix    = "{$width}x{$height}";
        $dest_path = "{$upload_dir}{$rel_path}-{$suffix}.{$ext}";
        $url       = "{$upload_url}{$rel_path}-{$suffix}.{$ext}";

        // If file exists: do nothing
        if (file_exists($dest_path)) {
            return $url;
        }

        // Generate thumbnail
        if (image_make_intermediate_size($path, $width, $height, $crop)) {
            return $url;
        }

        // Fallback to full size
        return "{$upload_url}{$rel_path}.{$ext}";
    }

}

/* Test code
add_action('loop_start',function(){
    $image = wp_get_attachment_image_src(10,array(500,500),false);
    echo '<img src="'.$image[0].'"/>';
});*/
