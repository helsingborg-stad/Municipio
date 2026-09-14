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
        $handle = @fopen($filePath, 'rb');
        if ($handle === false) {
            return false;
        }

        try {
            $header = stream_get_contents($handle, 12);
            if (
                !is_string($header)
                || strlen($header) < 12
                || substr($header, 0, 4) !== 'RIFF'
                || substr($header, 8, 4) !== 'WEBP'
            ) {
                return false;
            }

            $riffHeader = unpack('Vsize', substr($header, 4, 4));
            if ($riffHeader === false || !isset($riffHeader['size'])) {
                return false;
            }
            $riffSize = $riffHeader['size'];
            $remainingBytes = max(0, $riffSize - 4);

            while ($remainingBytes >= 8) {
                $chunkHeader = stream_get_contents($handle, 8);
                if (!is_string($chunkHeader) || strlen($chunkHeader) < 8) {
                    return false;
                }

                $chunkType = substr($chunkHeader, 0, 4);
                $chunkSizeData = unpack('Vsize', substr($chunkHeader, 4, 4));
                if ($chunkSizeData === false || !isset($chunkSizeData['size'])) {
                    return false;
                }
                $chunkSize = $chunkSizeData['size'];
                $remainingBytes -= 8;

                $previewLength = match ($chunkType) {
                    'VP8L' => 5,
                    'VP8X' => 1,
                    default => 0,
                };

                $preview = $previewLength > 0 ? stream_get_contents($handle, min($chunkSize, $previewLength)) : '';
                $remainingBytes -= min($chunkSize, $previewLength);

                if ($chunkType === 'VP8X' && is_string($preview) && isset($preview[0])) {
                    return (ord($preview[0]) & 0x10) === 0x10;
                }

                if ($chunkType === 'VP8L' && is_string($preview) && isset($preview[4])) {
                    return (ord($preview[4]) & 0x10) === 0x10;
                }

                $bytesToSkip = ($chunkSize - strlen($preview)) + ($chunkSize % 2);
                if ($bytesToSkip > 0 && !$this->skipStreamBytes($handle, $bytesToSkip)) {
                    return false;
                }

                $remainingBytes -= $bytesToSkip;
            }
        } finally {
            fclose($handle);
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
     * Consume bytes from a stream without loading the entire file into memory.
     *
     * @param resource $handle
     * @param int $bytesToSkip
     * @return bool
     */
    private function skipStreamBytes($handle, int $bytesToSkip): bool
    {
        while ($bytesToSkip > 0) {
            $chunk = stream_get_contents($handle, min($bytesToSkip, 8192));
            if (!is_string($chunk) || $chunk === '') {
                return false;
            }

            $bytesToSkip -= strlen($chunk);
        }

        return true;
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
