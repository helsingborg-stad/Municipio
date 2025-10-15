<?php

namespace Municipio\ImageFocus;

use Municipio\ImageFocus\Resolver\FocusPointResolverInterface;
use Municipio\ImageFocus\Storage\FocusPointStorage;
use WpService\WpService;

class ImageFocusManager
{
    public function __construct(
        private WpService $wp,
        private FocusPointStorage $storage,
        private FocusPointResolverInterface $resolver
    ) {}

    public function calculate(int $attachmentId, array $metadata, string $context): array
    {
        if ($context !== 'create' || !$this->isImage($attachmentId)) {
            return $metadata;
        }

        if ($this->storage->get($attachmentId) !== null) {
            return $metadata;
        }

        $filePath = $this->wp->getAttachedFile($attachmentId);
        if (!$filePath || !file_exists($filePath)) {
            return $metadata;
        }

        $focus = $this->resolver->resolve($filePath, $metadata['width'], $metadata['height'], $attachmentId);
        if ($focus === null) {
            return $metadata;
        }

        $this->storage->set($attachmentId, $focus);
        return $metadata;
    }

    private function isImage(int $attachmentId): bool
    {
        $mime = $this->wp->getPostMimeType($attachmentId);
        return strpos($mime, 'image/') === 0;
    }
}