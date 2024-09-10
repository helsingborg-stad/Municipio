<?php

namespace Municipio\ImageConvert\Contract;

class ImageContract implements ImageContractInterface
{
    private string $url;
    private int|string|null $height;
    private int|string|null $width;
    private static array $attachmentUrlRuntimeCache = [];

    // Constructor using property promotion
    public function __construct(private int $id, int|string|bool|null $height, int|string|bool|null $width)
    {
        $this->height = $this->sanitizeDimension($height, 'height');
        $this->width = $this->sanitizeDimension($width, 'width');
        $this->url = $this->createAttachmentUrl($id);
    }

    private function createAttachmentUrl(int $id): string
    {
        if(array_key_exists($id, self::$attachmentUrlRuntimeCache)) {
            return self::$attachmentUrlRuntimeCache[$id];
        }
        return self::$attachmentUrlRuntimeCache[$id] = wp_get_attachment_url($id);
    }

    private function sanitizeDimension(int|string|bool|null $dimension, string $name): int|string|null
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

    public function getDimensions(): array
    {
        return [
            0 => $this->width,
            1 => $this->height,
            'width' => $this->width,
            'height' => $this->height
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

    public static function factory(int $id, int|string|bool|null $height, int|string|bool|null $width): self
    {
        return new self($id, $height, $width);
    }
}