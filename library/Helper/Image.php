<?php

namespace Municipio\Helper;

use WP_Error;
use WP_Post;
use WP_Query;
use Municipio\Helper\File as FileHelper;
class Image
{
    public const SIDELOADED_IDENTIFIER_KEY = "sideloaded_identifier";

    /**
     * Resizes an image to a specified size
     * @param  integer|string  $originalImage Attachment id, path or url
     * @param  integer         $width         Target width
     * @param  integer         $height        Target height
     * @param  boolean         $crop          Crop or not?
     * @return string                         Image url
     */
    public static function resize($originalImage, $width, $height, $crop = true)
    {
        $imagePath = false;

        // Image from attachment id
        if (is_numeric($originalImage)) {
            $imagePath = wp_get_attachment_url($originalImage);
        } elseif (in_array(substr($originalImage, 0, 7), array('https:/', 'http://'))) {
            $imagePath = self::urlToPath($originalImage);
        }

        if (!$imagePath) {
            return false;
        }

        $imagePath = self::removeImageSize($imagePath);

        if (!FileHelper::fileExists($imagePath)) {
            return false;
        }

        $imagePathInfo = pathinfo($imagePath);

        $ext       = $imagePathInfo['extension'];
        $suffix    = "{$width}x{$height}";
        $destPath = "{$imagePathInfo['dirname']}/{$imagePathInfo['filename']}-{$suffix}.{$ext}";

        if (FileHelper::fileExists($destPath)) {
            return self::pathToUrl($destPath);
        }

        if (image_make_intermediate_size($imagePath, $width, $height, $crop)) {
            return self::pathToUrl($destPath);
        }

        return $originalImage;
    }

    /**
     * Removes image size suffix from filename
     * @param  string $filename Filename
     * @return string           Filename
     */
    public static function removeImageSize($filename)
    {
        return preg_replace('/-(\d+)x(\d+).(jpg|png|gif|bmp|tif)$/i', ".$3", $filename);
    }

    /**
     * A very simple way of making a url into a path.
     * This presumes that your url has the same strucutre as your path (i.e does not handle url rewrites)
     * @param  string $url The url to make into a path
     * @return string      Path
     */
    public static function urlToPath($url)
    {
        $path = str_replace('http://', '', $url);
        $path = str_replace('https://', '', $path);
        $path = str_replace($_SERVER['HTTP_HOST'], '', $path);

        if (substr($path, 0, 1) == '/') {
            $path = substr($path, 1);
        }

        return $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
    }

    /**
     * A very simple way of making a path into a url.
     * This persumes that your path has the same structure as your url (i.e does not handle url rewrites)
     * @param  string $path Path
     * @return string       Url
     */
    public static function pathToUrl($path)
    {
        $url = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
        return '//' . $_SERVER['HTTP_HOST'] . $url;
    }

    /**
     * Add a sideloaded identifier to an attachment.
     * This identifier is used to avoid sideloading the same attachment multiple times.
     *
     * @param int $attachmentId The ID of the attachment to add the sideloaded identifier to.
     * @return bool|WP_Error Returns true on success, a WP_Error object on failure.
    */
    public static function addSideloadedIdentifierToAttachment(int $attachmentId)
    {
        $file = get_attached_file($attachmentId);

        if ($file === false) {
            return new WP_Error('file-not-found', __('File not found when setting sideloaded identifier.'));
        }

        $fileHash = md5_file($file);

        if ($fileHash === false) {
            return new WP_Error('identifier-not-generated', __('Could not generate sideloaded identifier for file.'));
        }

        update_post_meta($attachmentId, self::SIDELOADED_IDENTIFIER_KEY, $fileHash);
    }

    /**
     * Get previously added attachment by remote URL.
     * Mainly used to avoid sideloading the same attachment multiple times.
     *
     * @param string $remoteUrl The URL of the remote file.
     * @return mixed Returns the attachment post object on success,
     * WP_Error on failure, or null if the attachment is not found.
    */
    public static function getAttachmentByRemoteUrl(string $remoteUrl)
    {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $file = download_url($remoteUrl);

        if (is_wp_error($file)) {
            return $file;
        }

        $fileHash = md5_file($file);

        if ($fileHash === false) {
            return null;
        }

        $foundPosts = get_posts(array(
            'post_type' => 'attachment',
            'meta_key'   => self::SIDELOADED_IDENTIFIER_KEY,
            'meta_value' => $fileHash,
            'posts_per_page' => 1
        ));

        if (empty($foundPosts)) {
            return null;
        }

        return $foundPosts[0];
    }
}
