<?php
namespace Municipio\Helper;

class VideoService
{
    protected $url;
    protected $videoId;
    protected $uploadsDir;
    protected $uploadsSubDir;
    protected $videoServiceDirname;
    protected $fileName;
    protected $filePath;
    public $coverArt;
    
    private $imageLocations = [
        'youtube'   => 'https://img.youtube.com/vi/%s/maxresdefault.jpg',
        'vimeo'     => 'https://vumbnail.com/%s.jpg'
    ];
    
    public function __construct(string $url)
    {
        $this->url     = $url;
        $this->videoId = $this->getVideoId($this->url);
        
        $this->uploadsDir          = $this->getUploadsDir();
        $this->videoServiceDirname = 'video-service-thumbnails';
        $this->uploadsSubDir       = $this->uploadsDir . '/' . $this->videoServiceDirname;
        $this->fileName            = $this->videoId . '.jpg';
        
        $this->filePath = implode("/", [
            $this->uploadsSubDir,
            $this->fileName
        ]);
        
        $this->coverArt = $this->getCoverArt();
    }
    public function getCoverArt(string $url = null)
    {
        if (empty($url)) {
            $url = $this->url;
        }
        
        return $this->maybeDownloadCoverArt($url);
    }
    private function maybeDownloadCoverArt()
    {
        if (!file_exists($this->filePath)) {
            $this->downloadCoverImage();
        }
        
        return $this->getLocalCoverUrl();
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
        if (empty($url)) {
            $url = $this->url;
        }
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
        if (empty($url)) {
            $url = $this->url;
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
     * @return string $videoId           The id in embed link
     */
    private function parseYoutubeId()
    {
        $id       = false;
        $url      = $this->url;
        $urlParts = parse_url($url);
        $hostname = $urlParts['host'];
        
        if ($hostname == 'youtu.be') {
            //https://youtu.be/ID
            $id = trim(rtrim(parse_url($url, PHP_URL_PATH), "/"), "/");
            
        } elseif (str_contains($urlParts['path'], 'embed')) {
            //https://www.youtube.com/embed/ID
            $id = trim(rtrim(str_replace('/embed/', '', $urlParts['path'])));
            
        } else {
            //https://www.youtube.com/watch?v=ID
            parse_str(
                parse_url($url, PHP_URL_QUERY),
                $queryParameters
            );
            if (isset($queryParameters['v']) && !empty($queryParameters['v'])) {
                $id = $queryParameters['v'];
            }
            
        }

        return $id;
    }
    /**
     * Get vimeo id from embed url
     *
     * @param  string $url    The embed link
     * @return string $videoId           The id in embed link
     */
    private function parseVimeoId()
    {
        $id    = false;
        $url   = $this->url;
        $parts = explode('/', $url);

        if (is_array($parts) & !empty($parts)) {
            foreach ($parts as $part) {
                if (is_numeric($part)) {
                    $id = $part;
                }
            }
        }
        return $id;
    }
    /**
     * > Download the cover image from the remote server and store it locally
     *
     * @return The file content of the cover image.
     */
    private function downloadCoverImage()
    {
        $coverUrl    = $this->getRemoteCoverUrl();
        $fileContent = $this->readRemoteFile($coverUrl);
        
        if ($fileContent) {
            return $this->storeImage($fileContent);
        }
        return false;
    }
    /**
     * It takes a file content and stores it in the file system
     *
     * @param fileContent The image data
     */
    private function storeImage($fileContent = false)
    {
        if (empty($fileContent)) {
            return false;
        }
        
        $fileSystem = $this->initFileSystem();
        
        if (!is_dir($this->uploadsSubDir)) {
            $fileSystem->mkdir($this->uploadsSubDir, FS_CHMOD_DIR);
        }
        
        if (!file_exists($this->filePath)) {
            return $fileSystem->put_contents(
                $this->filePath,
                $fileContent,
                FS_CHMOD_FILE
            );
        }

        return false;
    }
    /**
     * It checks if the file exists in the local filesystem, and if it does, it returns the URL to the
     * file
     *
     * @return The URL of the cover image.
     */
    public function getLocalCoverUrl()
    {
        $fileSystem = $this->initFileSystem();
       
        if ($fileSystem->is_file($this->filePath)) {
            return wp_upload_dir(null, false)['baseurl'] . "/{$this->videoServiceDirname}/{$this->fileName}";
        }
        
        return false;
    }
    /**
    * It takes a video service and a video id, and returns the url of the video's cover image
    *
    * @param string id The video ID.
    * @param string videoService The video service you want to get the cover image for.
    *
    * @return The cover image url for the video.
    */
    public function getRemoteCoverUrl(string $videoId = null, string $videoService = null)
    {
        if (empty($videoService) || empty($videoId)) {
            $url = $this->url;
        }
        
        if (empty($videoService)) {
            $videoService = $this->detectVideoService($url);
        }
        
        if (empty($videoId)) {
            $videoId = $this->getVideoId($url);
        }
        
        if (isset($this->imageLocations[$videoService])) {
            return sprintf($this->imageLocations[$videoService], $videoId);
        }
        return false;
    }
   /**
    * It reads a remote file and returns the body of the file.
    *
    * @param string url The URL of the remote file.
    *
    * @return the response body of the remote request.
    */
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
}
