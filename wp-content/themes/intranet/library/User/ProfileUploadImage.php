<?php

namespace Intranet\User;

class ProfileUploadImage
{
    private $user, $uploadDir, $sizes;
    private $imageDataUri, $decodedImage, $fileType;

    public function __construct()
    {
        $this->setCrop();
        $this->setUploadDir();
    }

    /**
     * Uploads user profile image
     * @param  string $imageDataUri The image data uri
     * @param  object $user         User object
     * @param  string $userMeta    User meta key
     * @return boolean
     */
    public function uploadProfileImage($imageDataUri, $user, $userMeta)
    {
        if (!isset($userMeta) && ! $userMeta) {
            return;
        }

        global $current_site;

        $this->user = $user;
        $this->createUploadDir();
        $this->decodeImageUri($imageDataUri);

        $imageDataUri = $this->imageDataUri;
        $decodedImage = $this->decodedImage;

        //Set Filetype
        $fileType = $this->setFileType($imageDataUri);

        //Set unique ID
        $imageId = uniqid();

        //Save original image
        $imagePaths = array();
        $imagePaths[] = $this->uploadDir . '/' . $user->data->user_login . '-' . $imageId . '.' . $fileType;
        file_put_contents($imagePaths[0], $decodedImage);

        //Crop & save images sizes
        $imagePaths = array_merge($imagePaths, $this->cropImages($imagePaths[0], $fileType, $imageId));

        //Remove old files & URLS from user meta
        $this->removeProfileImage($user->ID, $userMeta);

        //Save new URLS to user meta
        $imageUrls = array();
        foreach ($imagePaths as $imagePath) {
            $imageUrls[] = $this->getProfileImageUrlFromPath($imagePath);
        }

        update_user_meta($user->ID, $userMeta, $imageUrls);

        //Add image to old profile image meta
        if ($userMeta == 'user_profile_img') {
            $this->removeProfileImage($user->ID, 'user_profile_picture');
            update_user_meta($user->ID, 'user_profile_picture', $imageUrls[1]);
        }

        return true;
    }

    /**
     * Set upload dir name
     * @param string $dirName Name of the directory to create
     * @return void
     */
    public function setUploadDir($dirName = 'profile-images')
    {
        $uploadDirName = '/';
        $uploadDirName .= $dirName;

        $uploadDir = wp_upload_dir();
        $uploadDirUrl = $uploadDir['baseurl'];
        $uploadDir = $uploadDir['basedir'];
        $uploadDir = $uploadDir . $uploadDirName;

        $this->uploadDir = $uploadDir;
    }

    /**
     * Set crop sizes
     * @param mixed(int/array)      $width      Width in pixels or array with multiple sizes
     * @param mixed(int)            $height     Height in pixels
     * @param mixed(boolean)        $crop       Crop or just resize? true to crop
     * @return void
     */
    public function setCrop($width = 220, $height = 220, $crop = true)
    {
        $this->sizes = array();

        if (! is_array($width)) {
            $this->sizes[] = (object) [
                'width'  => $width,
                'height' => $height,
                'crop'   => $crop
            ];

            return;
        }

        //If array, set multiple sizes
        $sizes = $width;

        foreach ($sizes as $size) {
            $width = $size->width;
            $height = $size->height;
            $crop = $size->crop;

            if (isset($width) && is_int($width) && isset($height) && is_int($height) && isset($crop) && is_bool($crop)) {
                $this->sizes[] = (object) [
                    'width'  => $width,
                    'height' => $height,
                    'crop'   => $crop
                ];
            }
        }

        return;
    }

    /**
     * Decode image URI
     * @param  string   $imageDataUri   The image data uri
     * @return void
     */
    public function decodeImageUri($imageDataUri)
    {
        $imageDataUri = str_replace(' ', '+', $imageDataUri);
        $imageDataUri = explode(',', $imageDataUri);
        $decodedImage = base64_decode($imageDataUri[1]);

        $this->imageDataUri = $imageDataUri;
        $this->decodedImage = $decodedImage;

        return;
    }

    /**
     * Set File type
     * @param  string   $imageDataUri     The image data uri
     * @return string
     */
    public function setFileType($imageDataUri)
    {
        $fileType = preg_match_all('/data:image\/(.*);/', $imageDataUri[0], $matches);
        if (!isset($matches[1][0])) {
            return;
        }

        $fileType = $matches[1][0];

        switch ($fileType) {
            case 'jpeg':
                $fileType = 'jpg';
                break;

            case 'png':
                $fileType = 'png';
                break;

            default:
                $fileType = $fileType;
                break;
        }

        return $fileType;
    }

    /**
     * Create upload folder
     * @return void
     */
    public function createUploadDir()
    {
        if (isset($this->uploadDir) && !file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
    }

    /**
     * Resize/crop profile image to given size
     * @param  string  $path      Path to original image
     * @param  string  $fileType  Image file type
     * @param  string  $imageId   Image unique ID
     * @return array              Array of paths to images
     */
    public function cropImages($path, $fileType, $imageId)
    {
        $image = wp_get_image_editor($path);
        $sizes = $this->sizes;

        if (is_wp_error($image)) {
            return;
        }

        $images = array();

        foreach ($sizes as $dimension) {
            $image = wp_get_image_editor($path);
            $image->set_quality(80);

            $width = $dimension->width;
            $height = $dimension->height;
            $crop = $dimension->crop;

            $image->resize($width, $height, $crop);
            $newFilePath = $this->uploadDir . '/' . $this->user->data->user_login . '-' . $imageId . '-' . $width . 'x' . $height . '.' . $fileType;
            $image->save($newFilePath);

            $images[] = $newFilePath;
        }

        return $images;
    }

    /**
     * Rewrite profile image path to url
     * @param  string $path   The path
     * @return string         The url
     */
    public function getProfileImageUrlFromPath($path)
    {
        $path = explode('wp-content/', $path)[1];
        $url = content_url($path);

        return $url;
    }

    /**
     * Rewrite profile image to path
     * @param  string $url The path
     * @return string      The url
     */
    public function getProfileImagePathFromUrl($url)
    {
        $url = explode('wp-content/', $url)[1];
        $path = WP_CONTENT_DIR . '/' . $url;

        return $path;
    }

    /**
     * Removes a user's profile image
     * @param  integer $userId The user's id
     * @return boolean
     */
    public function removeProfileImage($userId, $userMeta)
    {
        if (!isset($userMeta) && ! $userMeta) {
            return;
        }

        $urls = get_user_meta($userId, $userMeta, true);

        if (!is_array($urls)) {
            return;
        }

        foreach ($urls as $url) {
            $path = $this->getProfileImagePathFromUrl($url);
            unlink($path);
        }

        delete_user_meta($userId, $userMeta);

        //Remove old meta field
        if ($userMeta == 'user_profile_img') {
            delete_user_meta($userId, 'user_profile_picture');
        }

        return true;
    }
}
