<?php

namespace Intranet\User;

class ProfileUploadImage
{
    private $user, $uploadDir, $width, $height, $crop, $imageDataUri, $decodedImage, $fileType, $dimensions, $filePaths, $fileUrls;

    public function __construct()
    {
        $this->setCrop();
        $this->setUploadDir();
    }

    /**
     * Uploads user profile image
     * @param  string $imageDataUri The image data uri
     * @param  object $user         User object
     * @return array                Profile image url
     */
    public function uploadProfileImage($imageDataUri, $user, $user_meta = 'user_profile_picture')
    {
        global $current_site;

        $this->user = $user;

        //switch_to_blog($current_site->blog_id);

        $this->decodeImageUri($imageDataUri);
        $this->setFileType();
        $this->createUploadDir();

        $this->filePaths = array();
        $this->filePaths[] = $this->uploadDir . '/' . $user->data->user_login . '-' . uniqid() . '.' . $this->fileType;

        //$filePathWithSize = $this->uploadDir . '/' . $user->data->user_login . '-' . uniqid() . '-' . $width . '-' . $heght . '.' . $this->fileType;

        file_put_contents($this->filePaths[0], $this->decodedImage);
        //restore_current_blog();


        $this->cropImages($this->filePaths[0]);

        $this->removeProfileImage($user->ID, $user_meta);

        $fileUrls = array();

        foreach($this->filePaths as $filePath) {
            $fileUrls[] = $this->getProfileImageUrlFromPath($filePath);
        }

        var_dump($fileUrls);
        update_user_meta($user->ID, $user_meta, $fileUrls);

        return true;
    }

    /**
     * Set upload dir name with
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
     * Set crop dimensions
     * @param mixed(int/array)      $width      Width in pixels
     * @param mixed(int)      $height     Height in pixels
     * @param mixed(boolean)  $crop       Crop or just resize? true to crop
     * @return void
     */
    public function setCrop($width = 220, $height = 220, $crop = true)
    {
        $this->dimensions = array();

        if(! is_array($width)) {
            $this->dimensions[] = (object) [
                'width'  => $width,
                'height' => $height,
                'crop'   => $crop
            ];

            return;
        }

        //If array, set multiple sizes
        $sizes = $width;

        foreach($sizes as $size) {
            $width = $size->width;
            $height = $size->height;
            $crop = $size->crop;

            if(isset($width) && is_int($width) && isset($height) && is_int($height) && isset($crop) && is_bool($crop)) {
                $this->dimensions[] = (object) [
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
    }

    /**
     * Set File type
     * @return void
     */
    public function setFileType()
    {
        $fileType = preg_match_all('/data:image\/(.*);/', $this->imageDataUri[0], $matches);
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

        $this->fileType = $fileType;
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
     * @param  integer  $width    Width in pixels
     * @param  integer  $height   Height in pixels
     * @param  boolean $crop      Crop or just resize? true to crop
     * @return string             The cropped image's path
     */
    public function cropImages($path)
    {
        $image = wp_get_image_editor($path);
        $dimensions = $this->dimensions;

        if (is_wp_error($image)) {
            return;
        }

        $image->set_quality(80);

        foreach($dimensions as $dimension) {

            $width = $dimension->width;
            $height = $dimension->height;
            $crop = $dimension->crop;

            $image->resize($width, $height, $crop);

            $newFilePath = $this->uploadDir . '/' . $this->user->data->user_login . '-' . uniqid() . '-' . $width . 'x' . $height . '.' . $this->fileType;

            $image->save($newFilePath);

            $this->filePaths[] = $newFilePath;
        }
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
     * Removes a user's profile iamge
     * @param  integer $userId The user's id
     * @return boolean
     */
    public function removeProfileImage($userId, $user_meta = 'user_profile_picture')
    {
        $urls = get_user_meta($userId, $user_meta, true);

        if (empty($urls)) {
            return true;
        }

        foreach($urls as $url) {
            $path = $this->getProfileImagePathFromUrl($url);
            unlink($path);
        }

        delete_user_meta($userId, $user_meta);

        return true;
    }
}
