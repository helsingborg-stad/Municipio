<?php

namespace Municipio\Helper;

use WP_Error;
use WP_Post;
use WP_Query;
use Municipio\Helper\File as FileHelper;

/**
 * Class Image
 * @package Municipio\Helper
 */
class Image
{
    public const SIDELOADED_IDENTIFIER_KEY = "sideloaded_identifier";

    /**
     * Resizes an image to a specified size
     * @param  integer|string  $originalImage Attachment id or url
     * @param  integer         $width         Target width
     * @param  integer         $height        Target height
     * @param  boolean         $crop          Crop or not?
     * @return string                         Image url
     */
    public static function resize($originalImage, $width, $height, $crop = true)
    {
        $imagePath = self::getImagePath($originalImage);
        $imagePath = self::removeImageSize($imagePath);

        if (empty($imagePath) || !FileHelper::fileExists($imagePath)) {
            return false;
        }

        $destinationPath = self::createDestinationPath($imagePath, $height, $width);

        if (
            (FileHelper::fileExists($destinationPath) ||
            image_make_intermediate_size($imagePath, $width, $height, $crop)) &&
            $newImage = self::pathToUrl($destinationPath)
        ) {
            return $newImage;
        }

        return $originalImage;
    }

    /**
     * Creates the path
     * @param  string|int $image Image ID or URL
     * @return string
     */
    private static function getImagePath($image): string
    {
        if (is_numeric($image)) {
            $image = wp_get_attachment_url($image);
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return self::urlToPath($image);
        }

        return "";
    }

    /**
     * Creates the path
     * @param  string $path Location to image
     * @return string
     */
    private static function createDestinationPath(string $path, int $width, int $height): string
    {
        $imagePathInfo = pathinfo($path);

        return $imagePathInfo['dirname'] . '/' . $imagePathInfo['filename'] . '-' . $width . 'x' . $height . '.'
        . $imagePathInfo['extension'];
    }

    /**
     * Removes image size suffix from filename
     * @param  string $filename Filename
     * @return string           Filename
     */
    private static function removeImageSize(string $filename): string
    {
        return preg_replace('/-(\d+)x(\d+).(jpg|png|gif|bmp|tif)$/i', ".$3", $filename);
    }

    /**
     * A very simple way of making a url into a path.
     * This presumes that your url has the same strucutre as your path (i.e does not handle url rewrites)
     * @param  string $url The url to make into a path
     * @return string|null path if server is defined
     */
    public static function urlToPath(string $url)
    {
        if (!isset($_SERVER['HTTP_HOST']) || !isset($_SERVER['DOCUMENT_ROOT'])) {
            return null;
        }

        $path = str_replace(['http://', 'https://', $_SERVER['HTTP_HOST']], '', $url);

        if (substr($path, 0, 1) == '/') {
            $path = substr($path, 1);
        }

        return $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
    }

    /**
     * A very simple way of making a path into a url.
     * This persumes that your path has the same structure as your url (i.e does not handle url rewrites)
     * @param  string $path Path
     * @return string|null Url if $_SERVER is defined
     */
    public static function pathToUrl(string $path)
    {
        if (!isset($_SERVER['DOCUMENT_ROOT']) || !isset($_SERVER['HTTP_HOST'])) {
            return null;
        }

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

        if (empty($file) || $file === false) {
            return new \WP_Error('file-not-found', __('File not found when setting sideloaded identifier.'));
        }

        $fileHash = md5_file($file);

        if ($fileHash === false) {
            return new \WP_Error('identifier-not-generated', __('Could not generate sideloaded identifier for file.'));
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
        self::includeFile();
        $file = download_url($remoteUrl);

        if (is_wp_error($file)) {
            return $file;
        }

        $fileHash = md5_file($file);

        if ($fileHash === false) {
            return null;
        }

        $foundPosts = get_posts(array(
        'post_type'      => 'attachment',
        'meta_key'       => self::SIDELOADED_IDENTIFIER_KEY,
        'meta_value'     => $fileHash,
        'posts_per_page' => 1
        ));

        if (empty($foundPosts)) {
            return null;
        }

        return $foundPosts[0];
    }

    /**
     * Requires file.php from wp-admin
     */
    protected static function includeFile()
    {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }

    /**
     * Gets correct image data
     *
     * @param int $id ID of the image attachment
     * @param array|string $size Size should be an array containing two int values (height and width). Can also be a string matching predefined sizes (ex. medium).
     * @return array
     */
    public static function getImageAttachmentData($id, $size = 'full')
    {
        $imageSrc = wp_get_attachment_image_src($id, $size);

        if (empty($imageSrc[0])) {
            return false;
        }

        $imageAlt         = get_post_meta($id, '_wp_attachment_image_alt', true);
        $imageTitle       = get_the_title($id);
        $imageCaption     = get_post_field('post_excerpt', $id);
        $imageDescription = get_post_field('post_content', $id);
        $imageByline      = get_post_meta($id, 'byline', true);

        $image = [
        'src'         => $imageSrc[0],
        'alt'         => $imageAlt ? $imageAlt : null,
        'title'       => $imageTitle ? $imageTitle : null,
        'caption'     => $imageCaption ? $imageCaption : null,
        'description' => $imageDescription ? $imageDescription : null,
        'byline'      => $imageByline ? $imageByline : null
        ];

        return $image;
    }
}
