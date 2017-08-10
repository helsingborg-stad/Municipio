<?php

namespace Intranet\User;

class ProfileUploadImage
{
    private $uploadDir, $width, $height, $crop, $imageDataUri, $decodedImage, $fileType, $filePathWithName;

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

        //switch_to_blog($current_site->blog_id);

        $this->decodeImageUri($imageDataUri);
        $this->setFileType();
        $this->createUploadDir();
        $this->saveImage();

        //restore_current_blog();

        //CROP IMAGE
        $croppedImagePath = $this->cropProfileImage($filePathWithName, $this->width, $this->height, $this->crop);
        $profileImageUrl = $this->getProfileImageUrlFromPath($croppedImagePath);
        $this->removeProfileImage($user->ID, $user_meta);

        update_user_meta($user->ID, $user_meta, $profileImageUrl);

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
     * Set crop dimensions
     * @param mixed(int/array)      $width      Width in pixels
     * @param mixed(int)      $height     Height in pixels
     * @param mixed(boolean)  $crop       Crop or just resize? true to crop
     * @return void
     */
    public function setCrop($width = 220, $height = 220, $crop = true)
    {
        $this->width = $width;
        $this->height = $height;
        $this->crop = $crop;
    }

    /**
     * Decode image URI
     * @param  string $imageDataUri     The image data uri
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
     * Decode image URI
     * @param  string $imageDataUri     The image data uri
     * @return void
     */
    public function createUploadDir($imageDataUri)
    {
        //Create upload dir
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
    }

    /**
     * Save original image
     * @return void
     */
    public function saveImage()
    {
        $this->filePathWithName = $this->uploadDir . '/' . $user->data->user_login . '-' . uniqid() . '.' . $this->fileType;
        file_put_contents($this->filePathWithName, $this->decodedImage);
    }

    /**
     * Resize/crop profile image to given size
     * @param  string  $path      Path to original image
     * @param  integer  $width    Width in pixels
     * @param  integer  $height   Height in pixels
     * @param  boolean $crop      Crop or just resize? true to crop
     * @return string             The cropped image's path
     */
    public function cropProfileImage($path, $width, $height, $crop = true)
    {
        $image = wp_get_image_editor($path);

        if (is_wp_error($image)) {
            return;
        }

        $image->set_quality(80);
        $image->resize($width, $height, $crop);
        $image->save($path);

        return $path;
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
        $imageUrl = get_user_meta($userId, $user_meta, true);

        if (empty($imageUrl)) {
            return true;
        }

        $imagePath = $this->getProfileImagePathFromUrl($imageUrl);
        $imagePath = preg_replace("/\.[^.]+$/", "", $imagePath);
        $images = glob($imagePath . '*');

        foreach ($images as $image) {
            unlink($image);
        }

        delete_user_meta($userId, $user_meta, $imageUrl);

        return true;
    }
}
