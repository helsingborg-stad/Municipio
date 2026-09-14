<?php

namespace Municipio\Integrations\Component;

use ComponentLibrary\Integrations\Image\ImageResolverInterface;

class ImageResolver implements ImageResolverInterface
{
    private const LQIP_WIDTH = 100;
    private const LQIP_HEIGHT = false;
    private const TRANSPARENCY_CAPABLE_MIME_TYPES = [
        'image/gif',
        'image/png',
        'image/webp',
    ];

    /**
     * Get image url
     *
     * @param int $id
     * @param array $size
     * @return string|null
     */
    public function getImageUrl(int $id, array $size): ?string
    {
        static $runtimeCache = [];
        $size = $this->normalizeSize($size);
        $cacheKey = md5($id . serialize($size));

        if (array_key_exists($cacheKey, $runtimeCache)) {
            return $runtimeCache[$cacheKey];
        }

        if ($this->isLqipRequest($size)) {
            return $runtimeCache[$cacheKey] = $this->resolveLqipUrl($id, $size);
        }

        return $runtimeCache[$cacheKey] = $this->resolveAttachmentImageUrl($id, $size);
    }

    /**
     * Resolve the LQIP URL for an attachment.
     *
     * @param int $id
     * @param array $size
     * @return string|null
     */
    private function resolveLqipUrl(int $id, array $size): ?string
    {
        $mimeType = get_post_mime_type($id);
        $hasTransparency = $this->sourceImageHasTransparency($id, $mimeType);
        $lqipUrl = $hasTransparency ? null : $this->resolveAttachmentImageUrl($id, $size);

        return apply_filters(
            'Municipio/Component/Image/LqipUrl',
            $lqipUrl,
            $id,
            $size,
            [
                'mimeType' => $mimeType,
                'hasTransparency' => $hasTransparency,
            ],
        );
    }

    /**
     * Resolve the image URL via WordPress.
     *
     * @param int $id
     * @param array $size
     * @return string|null
     */
    private function resolveAttachmentImageUrl(int $id, array $size): ?string
    {
        $image = wp_get_attachment_image_src($id, $size);

        if ($image !== false && isset($image[0]) && filter_var($image[0], FILTER_VALIDATE_URL)) {
            return $image[0];
        }

        return null;
    }

    /**
     * Determine whether the original image has transparency.
     *
     * @param int $id
     * @param mixed $mimeType
     * @return bool
     */
    private function sourceImageHasTransparency(int $id, mixed $mimeType): bool
    {
        static $transparencyCache = [];

        if (!is_string($mimeType) || !in_array($mimeType, self::TRANSPARENCY_CAPABLE_MIME_TYPES, true)) {
            return false;
        }

        $filePath = get_attached_file($id);
        if (!is_string($filePath) || $filePath === '') {
            return false;
        }

        $cacheKey = md5($id . '|' . $mimeType . '|' . $filePath);
        if (array_key_exists($cacheKey, $transparencyCache)) {
            return $transparencyCache[$cacheKey];
        }

        return $transparencyCache[$cacheKey] = match ($mimeType) {
            'image/gif' => $this->gifHasTransparency($filePath),
            'image/png' => $this->pngHasTransparency($filePath),
            'image/webp' => $this->webpHasTransparency($filePath),
            default => false,
        };
    }

    /**
     * Determine whether a PNG file has transparency.
     *
     * @param string $filePath
     * @return bool
     */
    private function pngHasTransparency(string $filePath): bool
    {
        $header = $this->readFileChunk($filePath, 8192);
        if (!is_string($header) || strlen($header) < 26 || !str_starts_with($header, "\x89PNG\r\n\x1a\n")) {
            return false;
        }

        $colorType = ord($header[25]);
        if (in_array($colorType, [4, 6], true)) {
            return true;
        }

        $headerBeforeImageData = strstr($header, 'IDAT', true);
        $transparencyScanWindow = is_string($headerBeforeImageData) ? $headerBeforeImageData : $header;

        return str_contains($transparencyScanWindow, 'tRNS');
    }

    /**
     * Determine whether a GIF file has transparency.
     *
     * @param string $filePath
     * @return bool
     */
    private function gifHasTransparency(string $filePath): bool
    {
        $header = $this->readFileChunk($filePath, 8192);
        if (
            !is_string($header)
            || (!str_starts_with($header, 'GIF87a') && !str_starts_with($header, 'GIF89a'))
        ) {
            return false;
        }

        $length = strlen($header) - 7;
        for ($offset = 0; $offset < $length; $offset++) {
            if (
                $header[$offset] === "\x21"
                && $header[$offset + 1] === "\xF9"
                && $header[$offset + 2] === "\x04"
                && (ord($header[$offset + 3]) & 0x01) === 0x01
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether a WebP file has transparency.
     *
     * @param string $filePath
     * @return bool
     */
    private function webpHasTransparency(string $filePath): bool
    {
        $header = $this->readFileChunk($filePath, 256);
        if (
            !is_string($header)
            || strlen($header) < 16
            || substr($header, 0, 4) !== 'RIFF'
            || substr($header, 8, 4) !== 'WEBP'
        ) {
            return false;
        }

        $offset = 12;
        while ($offset + 8 <= strlen($header)) {
            $chunkType = substr($header, $offset, 4);
            $chunkSizeBytes = substr($header, $offset + 4, 4);
            if (strlen($chunkSizeBytes) < 4) {
                return false;
            }

            $chunkSize = unpack('V', $chunkSizeBytes)[1];
            $chunkDataOffset = $offset + 8;

            if ($chunkType === 'ALPH') {
                return true;
            }

            if ($chunkType === 'VP8X' && isset($header[$chunkDataOffset])) {
                return (ord($header[$chunkDataOffset]) & 0x10) === 0x10;
            }

            if ($chunkType === 'VP8L' && isset($header[$chunkDataOffset + 4])) {
                return (ord($header[$chunkDataOffset + 4]) & 0x10) === 0x10;
            }

            $offset = $chunkDataOffset + $chunkSize + ($chunkSize % 2);
        }

        return false;
    }

    /**
     * Read a small chunk from a file or stream.
     *
     * @param string $filePath
     * @param int $length
     * @return string|false
     */
    private function readFileChunk(string $filePath, int $length): string|false
    {
        $handle = @fopen($filePath, 'rb');
        if ($handle === false) {
            return false;
        }

        try {
            return stream_get_contents($handle, $length);
        } finally {
            fclose($handle);
        }
    }

    /**
     * Determine whether the requested size is the component library LQIP size.
     *
     * @param array $size
     * @return bool
     */
    private function isLqipRequest(array $size): bool
    {
        return ($size[0] ?? null) === self::LQIP_WIDTH && ($size[1] ?? null) === self::LQIP_HEIGHT;
    }

    /**
     * Normalize size values so runtime cache keys are stable.
     *
     * @param array $size
     * @return array
     */
    private function normalizeSize(array $size): array
    {
        return array_map(
            static fn(mixed $value): mixed => $value === 0 ? false : $value,
            $size,
        );
    }

    /**
     * Get image alt
     *
     * @param int $id
     * @return string|null
     */
    public function getImageAltText(int $id): ?string
    {
        $alt = get_post_meta($id, '_wp_attachment_image_alt', true);
        if ($alt) {
            return $alt;
        }
        return null;
    }
}
