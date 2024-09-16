<?php

namespace Municipio\ImageConvert\Contract;

use WpService\Contracts\GetAttachmentUrl;
use WpService\Contracts\GetAttachedFile;

class ImageContract implements ImageContractInterface
{
    private string $url;
    private string $path;
    private int|string|null $width;
    private int|string|null $height;
    private static array $attachmentRuntimeCache = [
        'url'  => [],
        'path' => []
    ];

    // Constructor using property promotion
    public function __construct(private GetAttachmentUrl&GetAttachedFile $wpService, private int $id, int|string|bool|null $width, int|string|bool|null $height)
    {
        $this->width  = $this->sanitizeDimension($width, 'width');
        $this->height = $this->sanitizeDimension($height, 'height');
        $this->url    = $this->createAttachmentUrl($id);
        $this->path   = $this->createAttachmentPath($id);
    }

    private function createAttachmentUrl(int $id): string
    {
        if (array_key_exists($id, self::$attachmentRuntimeCache)) {
            return self::$attachmentRuntimeCache['url'][$id];
        }
        return self::$attachmentRuntimeCache['url'][$id] = $this->wpService->getAttachmentUrl($id);
    }

    private function createAttachmentPath(int $id): string
    {
        if (array_key_exists($id, self::$attachmentRuntimeCache)) {
            return self::$attachmentRuntimeCache['path'][$id];
        }
        return self::$attachmentRuntimeCache['path'][$id] = $this->wpService->getAttachedFile($id);
    }

    public function sanitizeDimension(int|string|bool|null $dimension, string $name): int|string|null
    {
        if ($dimension === true) {
            throw new \InvalidArgumentException("Image property '$name' cannot be boolean true. Must be an integer, string, null or false (casted to null).");
        }

        if ($dimension === false || $dimension === null) {
            return null;
        }

        return is_numeric($dimension) ? (int) $dimension : $dimension;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getDimensions(): array
    {
        return [
            0        => $this->width,
            1        => $this->height,
            'width'  => $this->width,
            'height' => $this->height
        ];
    }

    public function getIntermidiateLocation(?string $suffix = null): array
    {
        $intermidiateString = function ($path, $width, $height, $suffix = null): string {
            $fileInfo = pathinfo($path);

            if (!isset($fileInfo['dirname'], $fileInfo['filename'], $fileInfo['extension'])) {
                return $path;
            }

            $dirname   = $fileInfo['dirname'];
            $filename  = $fileInfo['filename'];
            $extension = $fileInfo['extension'];

            if (!is_null($suffix)) {
                $extension = $suffix;
            }

            return $dirname . '/' . $filename . '-' . $width . 'x' . $height . '.' . $extension;
        };

        return [
            'url'  => $intermidiateString($this->url, $this->getWidth(), $this->getHeight(), $suffix),
            'path' => $intermidiateString($this->path, $this->getWidth(), $this->getHeight(), $suffix)
        ];
    }

    public function getWidth(): int|string|null
    {
        return $this->width;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function getHeight(): int|string|null
    {
        return $this->height;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public static function factory(GetAttachmentUrl&GetAttachedFile $wpService, int $id, int|string|bool|null $width, int|string|bool|null $height): self
    {
        return new self($wpService, $id, $width, $height);
    }
}
