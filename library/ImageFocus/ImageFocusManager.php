<?php

namespace Municipio\ImageFocus;

use Municipio\ImageFocus\Resolvers\FocusPointResolverInterface;
use Municipio\ImageFocus\Storage\FocusPointStorage;
use WpService\WpService;

class ImageFocusManager
{
    public function __construct(
        private WpService $wpService,
        private FocusPointStorage $storage,
        private FocusPointResolverInterface $resolver
    ) {}

    public function calculate(int $attachmentId, array $metadata, string $context): array
    {
        if(!$this->isManualUpload()) {
            return $metadata;
        }

        if ($context !== 'create' || !$this->isImage($attachmentId)) {
            return $metadata;
        }

        if ($this->storage->get($attachmentId) !== null) {
            return $metadata;
        }

        $filePath = $this->wpService->getAttachedFile($attachmentId);
        if (!$this->fileExists($filePath)) {
            return $metadata;
        }

        $focus = $this->resolver->resolve($filePath, $metadata['width'], $metadata['height'], $attachmentId);
        if ($focus === null) {
            return $metadata;
        }

        $this->storage->set($attachmentId, $focus);
        
        return $metadata;
    }

    /**
     * Check if attachment is an image
     * 
     * @param int $attachmentId
     * @return bool
     */
    private function isImage(int $attachmentId): bool
    {
        $mime = $this->wpService->getPostMimeType($attachmentId);
        return strpos($mime, 'image/') === 0;
    }

    /**
     * Check if the current request is a manual upload via AJAX.
     * 
     * @return bool
     */
    private function isManualUpload(): bool
    {
        return defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action']) && $_POST['action'] === 'upload-attachment';
    }

    /**
     * Check if file exists
     * Check if file exists
     * 
     * @param string $filePath
     * @return bool
     */
    private function fileExists(string $filePath): bool
    {
        if(empty($filePath)) {
          return false;
        }
        return file_exists($filePath);
    }
}
