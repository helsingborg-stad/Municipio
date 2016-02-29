<?php

namespace Municipio\Theme;

class OnTheFlyImages
{
    public function __construct()
    {
        add_filter('image_downsize', array($this, 'runResizeImage'), 5, 3);
        add_filter('image_resize_dimensions', array($this, 'upscaleThumbnail'), 10, 6);
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
        if (is_array($size) && count($size) == 2) {
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

    /* Upscale images when thhey are to small
     *
     * @param  int     $orig_w        Original width
     * @param  int     $orig_h        Original height
     * @param  int     $new_w         New width
     * @param  int     $new_h         New height
     * @param  bool    $crop          Crop or not
     *
     * @return Array                  Array with new dimension
     */
    public function upscaleThumbnail($default, $orig_w, $orig_h, $new_w, $new_h, $crop)
    {
        if (!$crop) {
            return null;
        } // let the wordpress default function handle this

        $size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

        $crop_w = round($new_w / $size_ratio);
        $crop_h = round($new_h / $size_ratio);

        $s_x = floor(($orig_w - $crop_w) / 2);
        $s_y = floor(($orig_h - $crop_h) / 2);

        if (is_array($crop)) {

            //Handles left, right and center (no change)
            if ($crop[ 0 ] === 'left') {
                $s_x = 0;
            } elseif ($crop[ 0 ] === 'right') {
                $s_x = $orig_w - $crop_w;
            }

            //Handles top, bottom and center (no change)
            if ($crop[ 1 ] === 'top') {
                $s_y = 0;
            } elseif ($crop[ 1 ] === 'bottom') {
                $s_y = $orig_h - $crop_h;
            }
        }

        return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
    }
}

/*
add_action('loop_start', function () {
    foreach (array(50, 100, 150, 200, 250, 300, 350, 400, 450, 500) as $key => $value) {
        $image = wp_get_attachment_image_src(10, array($value, $value*2), false);
        echo '<img src="'.$image[0].'"/>';
    }
});
*/
