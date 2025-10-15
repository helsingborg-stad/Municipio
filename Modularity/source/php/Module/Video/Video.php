<?php

namespace Modularity\Module\Video;

class Video extends \Modularity\Module
{
    public $slug = 'video';
    public $supports = array();
    private $imageLocations = [
        'youtube'   => 'https://img.youtube.com/vi/%s/maxresdefault.jpg',
        'vimeo'     => 'https://vumbnail.com/%s.jpg'
    ];

    public function init()
    {
        $this->nameSingular = __("Video", 'modularity');
        $this->namePlural = __("Video", 'modularity');
        $this->description = __("Outputs an embedded Video.", 'modularity');

        //Cover images
        add_action('wp_after_insert_post', array($this, 'getVideoCover'), 10, 4);
        
        add_action('Modularity/save_block', array($this, 'getVideoCoverForBlock'), 10, 3);

        //Add mime types
        add_filter('upload_mimes', array($this, 'addVttFormatAsAllowedFiletype'), 10, 1);
    }

    /**
     * Add allowed file types
     *
     * @param array $mimes Mime list without vtt
     * @return array $mimes Mime list with vtt
     */
    public function addVttFormatAsAllowedFiletype($mimes)
    {
        $mimes['vtt']  = 'text/vtt';
        return $mimes;
    }

    /**
     * Protects function to be runned in cron, or autosave.
     *
     * @return boolean  Should run get methods.
     */
    private function shouldSave()
    {
        //Bail early if autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }

        //Bail early if cron
        if (defined('DOING_CRON') && DOING_CRON) {
            return false;
        }

        return true;
    }

    /**
     * Detect video service from url.
     *
     * @param string        $url The embed url
     * @return string|bool       Video service name, or false if not detected.
     */
    private function detectVideoService($url)
    {
        if (str_contains($url, 'vimeo')) {
            return 'vimeo';
        }
        if (str_contains($url, 'youtu')) { //Matches youtu.be and full domain
            return 'youtube';
        }
        return false;
    }

    /**
     * Hook to integrate with module.
     * Get and store file.
     *
     * @param string        $postId         The id of current module
     * @param string        $post           Full post object for current module
     * @param string        $isUpdate       If module is a new module, or should update an existing module.
     * @return string|bool                  Name of the file, false if not downloaded.
     */
    public function getVideoCover($postId, $post, $isUpdate, $postBeforeUpdate)
    {
        if (!$this->shouldSave()) {
            return false;
        }

        if (get_post_type($postId) != 'mod-video') {
            return false;
        }

        if (!$this->isEmbed(get_field('type', $postId))) {
            delete_post_meta($postId, 'placeholder_fallback_image');
            return false;
        }

        $coverImage = get_field('placeholder_image', $postId);
        $embedUrl   = get_field('embed_link', $postId);

        if ($coverImage === false && filter_var($embedUrl, FILTER_VALIDATE_URL) !== false) {
            $videoService   = $this->detectVideoService($embedUrl);
            $videoId        = $this->getVideoId($embedUrl, $videoService);
            $coverImage     = $this->getCoverUrl($videoId, $videoService);

            $filePath = $this->downloadCoverImage($coverImage, $videoId);

            if ($filePath) {
                update_post_meta(
                    $postId,
                    'placeholder_fallback_image',
                    $filePath
                );
                return $filePath;
            }
        }

        delete_post_meta($postId, 'placeholder_fallback_image');

        return false;
    }
    
    /**
     * It checks if the block is a video block, if it's an embed, if it has no cover
     * image and if it has a valid embed URL.
     * If all of these conditions are met, it downloads the cover image from the
     * video service and saves it as an attachment in the media library and updates
     * the block cover image * with the attachment id of that image.
     *
     * @param block The block object
     * @param postId The id of the post that is being saved.
     * @param post The post object
     *
     * @return the ID of the attachment if it was successfully created, otherwise it returns false.
     */
    public function getVideoCoverForBlock($block, $postId, $post)
    {
        if (!$this->shouldSave() || 'acf/video' !== $block['blockName']) {
            return false;
        }
                
        $blockData = $block['attrs']['data'];
        $embedUrl  = $blockData['embed_link'];
        
        if (empty($blockData['placeholder_image']) && filter_var($embedUrl, FILTER_VALIDATE_URL) !== false && $this->isEmbed($blockData['type'])) {
            $placeholderImageFieldKey = $blockData['_placeholder_image'];

            $videoService   = $this->detectVideoService($embedUrl);
            $videoId        = $this->getVideoId($embedUrl, $videoService);
            $coverImage     = $this->getCoverUrl($videoId, $videoService);
            
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $attachmentId = \media_sideload_image($coverImage, $postId, sprintf(__('Automatically downloaded cover image for a video embedded in a post (post id: %s).', 'modularity'), $postId), 'id');
            
            if ($attachmentId) {
                $existingBlocks = parse_blocks($post->post_content);
                foreach ($existingBlocks as &$existingBlock) {
                    if ($existingBlock['attrs']['data'] === $block['attrs']['data']) {
                        $existingBlock['attrs']['data']['placeholder_image'] = $attachmentId;
                    }
                }
                   
                $postData = array(
                    'ID' => $postId,
                    'post_content' => serialize_blocks($existingBlocks),
                );
                
                update_field($placeholderImageFieldKey, $attachmentId);
                return wp_update_post($postData, true, false);
            }
        }
        
        return false;
    }
    
    /**
     * Check if embed option is enabled
     *
     * @param string $type
     * @return boolean
     */
    public function isEmbed($type)
    {
        return $type == 'embed' ? true : false;
    }

    /**
     * Download and store file
     *
     * @param string        $url           Url where asset can be found.
     * @param string        $videoId       Id of the video connected to the file.
     * @return string|bool                 Name of the file, false if not downloaded.
     */
    private function downloadCoverImage($url, $videoId)
    {
        if ($fileContents = $this->readRemoteFile($url)) {
            return $this->storeImage($fileContents, $videoId);
        }
        return false;
    }

    /**
     * Get the thumbnail from remote service.
     *
     * @param string        $url    Url where asset can be found.
     * @return string|bool          Contents of the image file, or false if not found.
     */
    private function readRemoteFile($url)
    {
        $responseHandle = wp_remote_get($url);

        if (!is_wp_error($responseHandle)) {
            $responseCode = wp_remote_retrieve_response_code($responseHandle);
            $responseMime = wp_remote_retrieve_header($responseHandle, 'content-type');
            $responseBody = wp_remote_retrieve_body($responseHandle);

            if ($responseCode == 200 && in_array($responseMime, ['image/jpeg', 'image/jpg'])) {
                return $responseBody;
            }
        }
        return false;
    }

    /**
     * Save image to filesystem.
     *
     * @param string        $fileContent   Contents of the file
     * @param string        $videoId       Id of the video connected to the file.
     * @return string|bool                 Name of the file, false if not written.
     */
    private function storeImage($fileContent, $videoId)
    {
        $uploadsDir     = $this->getUploadsDir();
        $uploadsSubDir  = $this->getUploadsSubdir();
        $fileSystem     = $this->initFileSystem();
        $fileName       = $videoId . ".jpg";

        //Explicit path
        $fullPath = implode("/", [
            $uploadsDir,
            $uploadsSubDir,
            $fileName
        ]);

        //Relative to uploads dir
        $subPath = implode("/", [
            $uploadsSubDir,
            $fileName
        ]);

        $fileSystem->put_contents(
            $fullPath,
            $fileContent,
            FS_CHMOD_FILE
        );

        if ($fileSystem->exists($fullPath)) {
            return $subPath;
        }

        return false;
    }

    /**
     * Get the wp file system instance.
     *
     * @return Object WP_Filesystem
     */
    private function initFileSystem()
    {
        require_once(ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
        global $wp_filesystem;
        return $wp_filesystem;
    }

    /**
     * Get the directory where uploaded files are located
     *
     * @return string Uploads
     */
    private function getUploadsDir()
    {
        return rtrim(wp_upload_dir()['basedir'], "/");
    }

    /**
     * Get the sub dir where the uploaded files are placed
     *
     * @return string Uploads subdir
     */
    private function getUploadsSubdir()
    {
        return trim(
            rtrim(
                wp_upload_dir()['subdir'],
                "/"
            ),
            "/"
        );
    }

    /**
     * Get id from embed url, switch beteen video services
     *
     * @param  string $embedLink    The embed link
     * @return string $id           The id in embed link
     */
    private function getVideoId($embedLink, $videoService)
    {
        if ($videoService == 'youtube') {
            return $this->parseYoutubeId($embedLink);
        }

        if ($videoService == 'vimeo') {
            return $this->parseVimeoId($embedLink);
        }

        return false;
    }

    /**
     * Get youtube id from embed url
     *
     * @param  string $embedLink    The embed link
     * @return string $id           The id in embed link
     */
    private function parseYoutubeId($embedLink)
    {
        $hostname = parse_url($embedLink, PHP_URL_HOST);

        //https://youtu.be/ID
        if ($hostname == 'youtu.be') {
            return trim(rtrim(parse_url($embedLink, PHP_URL_PATH), "/"), "/");
        }

        //https://www.youtube.com/watch?v=ID
        parse_str(
            parse_url($embedLink, PHP_URL_QUERY),
            $queryParameters
        );
        if (isset($queryParameters['v']) && !empty($queryParameters['v'])) {
            return $queryParameters['v'];
        }

        return false;
    }

    /**
     * Get vimeo id from embed url
     *
     * @param  string $embedLink    The embed link
     * @return string $id           The id in embed link
     */
    private function parseVimeoId($embedLink)
    {
        $parts = explode('/', $embedLink);

        if (is_array($parts) & !empty($parts)) {
            foreach ($parts as $part) {
                if (is_numeric($part)) {
                    return $part;
                }
            }
        }
        return false;
    }

    /**
     * Create a url where the cover image will be found.
     *
     * @param  string $id               The embed id
     * @param  string $videoService     What video service to use
     * @return string $url              A https url to the image
     */
    private function getCoverUrl($id, $videoService)
    {
        if (isset($this->imageLocations[$videoService])) {
            return sprintf($this->imageLocations[$videoService], $id);
        }
        return false;
    }

    /**
     * Manage view data
     *
     * @return array
     */
    public function data(): array
    {
        $data = $this->getFields();
        $data['id']         = uniqid('embed');
        if ($data['type'] == 'embed') {
            $data['embedCode'] = $this->getEmbedMarkup($data['embed_link']);
        }
        
        // Image
        $data['image'] = false;
        if (isset($data['placeholder_image']) && !empty($data['placeholder_image'])) {
            $data['image'] = wp_get_attachment_image_src(
                $data['placeholder_image']['id'],
                [1140, 641]
            )[0];
        }

        // Fallback image
        if (!$data['image']) {
            $fallbackImage = get_post_meta($this->ID, 'placeholder_fallback_image', true);
            if ($fallbackImage) {
                $data['image'] =  wp_get_upload_dir()['baseurl'] . "/" . $fallbackImage;
            }
        }

        //Uploaded
        if ($data['type'] == 'upload') {
            $data['source'] = $data['video_mp4']['url'];
        }

        //Lang
        $data['lang'] = (object) [
            'embedFailed' => __('This video could not be embedded. <a href="%s" target="_blank">View the video by visiting embedded page.</a>', 'modularity'),
        ];

        return $data;
    }

    /**
     * Embed
     *
     * @param [type] $embedLink
     * @return bool|string
     */
    private function getEmbedMarkup($embedLink)
    {
        return wp_oembed_get(
            $embedLink,
            array(
                'width' => 1080,
                'height' => 720
            )
        );
    }

    public function style()
    {
        wp_register_style('mod-video-style', MODULARITY_URL . '/dist/'
        . \Modularity\Helper\CacheBust::name('css/video.css'));

        wp_enqueue_style('mod-video-style');
    }

    public function script()
    {
        wp_register_script('mod-video-script', MODULARITY_URL . '/dist/'
        . \Modularity\Helper\CacheBust::name('js/video.js'));

        wp_enqueue_script('mod-video-script');
    }

    private function accessProtected($obj, $prop)
    {
        $reflection = new ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }

    /**
     * Available "magic" methods for modules:
     * init()            What to do on initialization
     * data()            Use to send data to view (return array)
     * style()           Enqueue style only when module is used on page
     * script            Enqueue script only when module is used on page
     * adminEnqueue()    Enqueue scripts for the module edit/add page in admin
     * template()        Return the view template (blade) the module should use when displayed
     */
}
