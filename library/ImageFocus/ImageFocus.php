<?php

namespace Municipio\ImageFocus;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;
use Imagick;

class ImageFocus implements Hookable
{
    private const META_KEY = '_focus_point';

    public function __construct(private WpService $wpService, private $focalPointDetector) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('wp_generate_attachment_metadata', [$this, 'calculateFocusPoint'], 10, 3);
    }

    public function calculateFocusPoint($metadata, $attachmentId, $context)
    {
        if ($context !== 'create') {
            return $metadata;
        }

        if ($this->getFocusPoint($attachmentId) !== null) {
            return $metadata;
        }

        if (!$this->isImageAttachment($attachmentId)) {
            return $metadata;
        }

        $filePath = $this->wpService->getAttachedFile($attachmentId);
        if (!$this->fileExists($filePath)) {
            return $metadata;
        }

        $focusPoint = $this->focalPointDetector->getPoint(new \Imagick($filePath));
        if ($focusPoint === null) {
            return $metadata;
        }

        $focusPoint = $this->pixelFocusToPercent(
            $focusPoint,
            $metadata['width'],
            $metadata['height']
        );
        $focusPoint = json_encode(array_map(fn($v) => intval($v), $focusPoint));

        $this->setFocusPoint($attachmentId, $focusPoint);

        return $metadata;
    }

    private function pixelFocusToPercent($focusPoint, $width, $height)
    {
        return [
            'left' => ($focusPoint->x / $width) * 100,
            'top' => ($focusPoint->y / $height) * 100,
        ];
    }

    /**
     * Set focus point on image
     *
     * @param int $imageId
     * @param array $focusPoint ['x' => float, 'y' => float]
     * @return bool
     */
    public function setFocusPoint($attachmentId, $focusPoint): bool
    {
        return (bool) $this->wpService->updatePostMeta($attachmentId, self::META_KEY, $focusPoint);
    }

    /**
     * Get focus point for image
     *
     * @param int $imageId
     * @return array|null
     */
    public function getFocusPoint($attachmentId): ?array
    {
        $focusPoint = $this->wpService->getPostMeta($attachmentId, self::META_KEY, true);
        return is_array($focusPoint) ? $focusPoint : null;
    }

    /**
     * Check if attachment is an image
     *
     * @param int $attachmentId
     * @return bool
     */
    private function isImageAttachment($attachmentId): bool
    {
        $mime = $this->wpService->getPostMimeType($attachmentId);
        return strpos($mime, 'image/') === 0;
    }

    /**
     * Check if file exists
     *
     * @param string $filePath
     * @return bool
     */
    private function fileExists($filePath): bool
    {
        if (empty($filePath)) {
            return false;
        }
        return file_exists($filePath);
    }
}
