<?php

namespace Municipio\Integrations\Component;

use ComponentLibrary\Integrations\Image\ImageResolverInterface;

class ImageResolver implements ImageResolverInterface
{
    private const LQIP_WIDTH = 100;
    private const LQIP_HEIGHT = false;
    private const WEBP_SCAN_BYTE_LIMIT = 65536;
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

        if ($this->isLqipRequest($size)) {
            return $this->resolveLqipUrl($id, $size);
        }

        $cacheKey = md5($id . serialize($size));
        if (array_key_exists($cacheKey, $runtimeCache)) {
            return $runtimeCache[$cacheKey];
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
        static $runtimeCache = [];
        static $attachedFileCache = [];

        $mimeType = get_post_mime_type($id);
        $attachedFileCacheKey = md5($id . '|' . (string) $mimeType);
        if (!array_key_exists($attachedFileCacheKey, $attachedFileCache)) {
            $attachedFileCache[$attachedFileCacheKey] = is_string($mimeType) && in_array($mimeType, self::TRANSPARENCY_CAPABLE_MIME_TYPES, true)
                ? get_attached_file($id)
                : null;
        }

        $filePath = $attachedFileCache[$attachedFileCacheKey];
        $cacheKey = md5(implode('|', [
            (string) $id,
            serialize($size),
            (string) $mimeType,
            is_string($filePath) ? $filePath : '',
        ]));

        if (!array_key_exists($cacheKey, $runtimeCache)) {
            $hasTransparency = $this->sourceImageHasTransparency($id, $mimeType, $filePath);
            $runtimeCache[$cacheKey] = [
                'url' => $hasTransparency ? null : $this->resolveAttachmentImageUrl($id, $size),
                'hasTransparency' => $hasTransparency,
            ];
        }

        $resolution = $runtimeCache[$cacheKey];
        $lqipUrl = $resolution['url'];
        $hasTransparency = $resolution['hasTransparency'];

        return $this->filterLqipUrl(
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
     * Apply filterable LQIP overrides.
     *
     * @param string|null $lqipUrl
     * @param int $id
     * @param array $size
     * @param array $context
     * @return string|null
     */
    protected function filterLqipUrl(?string $lqipUrl, int $id, array $size, array $context): ?string
    {
        return apply_filters('Municipio/Component/Image/LqipUrl', $lqipUrl, $id, $size, $context);
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
    private function sourceImageHasTransparency(int $id, mixed $mimeType, mixed $filePath = null): bool
    {
        if (!is_string($mimeType) || !in_array($mimeType, self::TRANSPARENCY_CAPABLE_MIME_TYPES, true)) {
            return false;
        }

        $filePath = $filePath ?? get_attached_file($id);
        if (!is_string($filePath) || $filePath === '') {
            return false;
        }

        return match ($mimeType) {
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
            $sawLossyAlphaChunk = false;
            $scannedBytes = 0;

            while ($remainingBytes >= 8) {
                if ($scannedBytes + 8 > self::WEBP_SCAN_BYTE_LIMIT) {
                    return false;
                }

                $chunkHeader = stream_get_contents($handle, 8);
                if (!is_string($chunkHeader) || strlen($chunkHeader) < 8) {
                    return false;
                }
                $scannedBytes += 8;

                $chunkType = substr($chunkHeader, 0, 4);
                $chunkSizeData = unpack('Vsize', substr($chunkHeader, 4, 4));
                if ($chunkSizeData === false || !isset($chunkSizeData['size'])) {
                    return false;
                }
                $chunkSize = $chunkSizeData['size'];
                $remainingBytes -= 8;

                $previewLength = match ($chunkType) {
                    'VP8L' => 5,
                    'VP8 ' => 6,
                    'VP8X' => 1,
                    default => 0,
                };

                if ($chunkType === 'ALPH') {
                    $sawLossyAlphaChunk = true;
                }

                $previewBytesToRead = min($chunkSize, $previewLength);
                $unreadPayloadBytes = $chunkSize - $previewBytesToRead;
                $paddingBytes = $chunkSize % 2;

                if ($scannedBytes + $previewBytesToRead + $unreadPayloadBytes + $paddingBytes > self::WEBP_SCAN_BYTE_LIMIT) {
                    return false;
                }

                $preview = $previewBytesToRead > 0 ? stream_get_contents($handle, $previewBytesToRead) : '';
                if ($preview === false || strlen($preview) !== $previewBytesToRead) {
                    return false;
                }

                $remainingBytes -= $previewBytesToRead;
                $scannedBytes += $previewBytesToRead;

                if ($chunkType === 'VP8X' && isset($preview[0]) && (ord($preview[0]) & 0x10) === 0x10) {
                    return true;
                }

                if (
                    $chunkType === 'VP8 '
                    && $sawLossyAlphaChunk
                    && strlen($preview) === 6
                    && substr($preview, 3, 3) === "\x9d\x01\x2a"
                ) {
                    return true;
                }

                if ($chunkType === 'VP8L' && strlen($preview) === 5 && ord($preview[0]) === 0x2f) {
                    $vp8lHeader = unpack('Vheader', substr($preview, 1, 4));
                    if ($vp8lHeader === false || !isset($vp8lHeader['header'])) {
                        return false;
                    }

                    if ((($vp8lHeader['header'] >> 28) & 0x01) === 1) {
                        return true;
                    }
                }

                if ($unreadPayloadBytes > 0 && !$this->skipStreamBytes($handle, $unreadPayloadBytes)) {
                    return false;
                }

                $remainingBytes -= $unreadPayloadBytes;
                $scannedBytes += $unreadPayloadBytes;

                if ($paddingBytes > 0 && !$this->skipStreamBytes($handle, $paddingBytes)) {
                    return false;
                }

                $remainingBytes -= $paddingBytes;
                $scannedBytes += $paddingBytes;
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
        if ($bytesToSkip <= 0) {
            return true;
        }

        if (@fseek($handle, $bytesToSkip, SEEK_CUR) === 0) {
            return true;
        }

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
        return ($size[0] ?? null) === self::LQIP_WIDTH
            && ($size[1] ?? null) === self::LQIP_HEIGHT;
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
