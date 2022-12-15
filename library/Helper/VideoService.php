<?php
namespace ComponentLibrary\Helper;

class VideoService
{
    protected $url;
    protected $videoId;
    protected $fileName;
    
    private $imageLocations = [
        'youtube'   => 'https://img.youtube.com/vi/%s/maxresdefault.jpg',
        'vimeo'     => 'https://vumbnail.com/%s.jpg'
    ];
    
    public function __construct(string $url, string $videoId = null)
    {
        $this->url     = $url;
        $this->videoId = $videoId || $this->getVideoId($this->url);
        
        $this->uploadsDir    = $this->getUploadsDir();
        $this->uploadsSubDir = $this->uploadsDir . 'video-service-thumbnail';
        
        $this->fileName      = $this->videoId . '.jpg';
        $this->filePath = implode("/", [
            $this->uploadsDir,
            $this->uploadsSubDir,
            $this->fileName
        ]);
        
    }
    
    /**
     * It checks if the url contains the string 'vimeo' or 'youtu' and returns the service name if it
     * does
     *
     * @param string url The url of the video
     *
     * @return A string of the video service name.
     */
    public function detectVideoService(string $url = null)
    {
        $url = $this->getUrl($url);
        
        if (str_contains($url, 'vimeo')) {
            return 'vimeo';
        }
        if (str_contains($url, 'youtu')) { //Matches youtu.be and full domain
            return 'youtube';
        }
        
        return false;
    }
   /**
    * It gets the video id from the url.
    *
    * @param string url The url of the video. If not provided, the url will be taken from the request.
    * @param string videoService The video service you want to use. If you don't specify one, it will
    * try to detect it.
    *
    * @return The video ID is being returned.
    */
    public function getVideoId(string $url = null, string $videoService = null)
    {
        if(empty($url)) {
            $url = $this->getUrl($url);
        }
        if (empty($videoService)) {
            $videoService = $this->detectVideoService($url);
        }
        
        if ($videoService == 'youtube') {
            return $this->parseYoutubeId($url);
        }

        if ($videoService == 'vimeo') {
            return $this->parseVimeoId($url);
        }

        return false;
    }

    /**
     * Get youtube id from embed url
     *
     * @param  string $url    The embed link
     * @return string $id           The id in embed link
     */
    private function parseYoutubeId(string $url = null)
    {
        $url = $this->getUrl($url);
        
        if (empty($url)) {
            return;
        }
        
        $urlParts = parse_url($url);
        $hostname = $urlParts['host'];
        
        //https://youtu.be/ID
        if ($hostname == 'youtu.be') {
            return trim(rtrim(parse_url($url, PHP_URL_PATH), "/"), "/");
        }
        
        //https://www.youtube.com/embed/ID
        if (str_contains($urlParts['path'], 'embed')) {
            return trim(rtrim(str_replace('/embed/', '', $urlParts['path'])));
        }
        
        //https://www.youtube.com/watch?v=ID
        parse_str(
            parse_url($url, PHP_URL_QUERY),
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
     * @param  string $url    The embed link
     * @return string $id           The id in embed link
     */
    private function parseVimeoId(string $url = null)
    {
        $url = $this->getUrl($url);
        if (empty($url)) {
            return;
        }
        $parts = explode('/', $url);

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
    * > This function returns the url if it's not empty, otherwise it returns the url property
    *
    * @param string url The URL to the API.
    *
    * @return The url property of the object.
    */
    private function getUrl(string $url = null)
    {
        if (empty($url)) {
            return $this->url;
        }
        return $url;
    }
   /**
    * > Download the cover image from the remote server and store it locally
    *
    * @param string url The url of the image to download.
    * @param string videoId The id of the video.
    *
    * @return The return value of the storeImage method.
    */
    private function downloadCoverImage(string $coverUrl = null, string $videoId = null)
    {
        if (empty($coverUrl)) {
            $coverUrl = $this->getCoverUrl();
        }
        if (empty($videoId)) {
            $videoId = $this->getVideoId($this->url);
        }
        
        if ($fileContents = $this->readRemoteFile($coverUrl)) {
            return $this->storeImage($fileContents, $videoId);
        }
        return false;
    }
    
    public function getLocalCoverUrl(string $id = null, string $coverUrl = null) {
        
        if(empty($coverUrl)) {
            $url = $this->getCoverUrl();
        }
        if(empty($id)) {
            $id = $this->getVideoId($this->url);
        }
        
        $fileSystem = $this->initFileSystem();
        
        if($fileSystem->is_file($this->fullPath)) {
            return wp_upload_dir( null, false )['url'] . '/' . $this->fileName;
        } else {
            if( $this->downloadCoverImage($coverUrl, $id) ) {
                return wp_upload_dir( null, false )['url'] . '/' . $this->fileName;
            }
        }
    }    
    /**
    * It takes a video service and a video id, and returns the url of the video's cover image
    *
    * @param string id The video ID.
    * @param string videoService The video service you want to get the cover image for.
    *
    * @return The cover image url for the video.
    */
    public function getCoverUrl(string $id = null, string $videoService = null)
    {
        if (empty($videoService) || empty($id)) {
            $url = $this->getUrl();
        }
        
        if (empty($videoService)) {
            $videoService = $this->detectVideoService($url);
        }
        
        if (empty($id)) {
            $id = $this->getVideoId($this->url);
        }
        
        if (isset($this->imageLocations[$videoService])) {
            return sprintf($this->imageLocations[$videoService], $id);
        }
        return false;
    }
    private function readRemoteFile(string $url = null)
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
    private function storeImage($fileContent = false)
    {
        if(empty($fileContent)) {
            return false;
        }
        
        $fileSystem = $this->initFileSystem(); 
        $fullPath   = $this->fullPath;

        if (!$fileSystem->exists($fullPath)) {
            return $fileSystem->put_contents(
                $fullPath,
                $fileContent,
                FS_CHMOD_FILE
            );
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
}
