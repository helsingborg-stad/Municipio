<?php

namespace Municipio\ImageFocus\Resolvers;

use Municipio\ImageFocus\Storage\FocusPointStorage;

class ManualInputFocusPointResolver implements FocusPointResolverInterface
{
    public function __construct(private FocusPointStorage $storage) {}

    public function isSupported(): bool
    {
        return true; // Always supported as it reads from storage
    }

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        if ($attachmentId === null) {
            return null;
        }

        return $this->storage->get($attachmentId);
    }
}