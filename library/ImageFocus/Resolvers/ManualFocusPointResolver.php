<?php

namespace Municipio\ImageFocus\Resolver;

use Municipio\ImageFocus\Storage\FocusPointStorage;

class ManualFocusPointResolver implements FocusPointResolverInterface
{
    public function __construct(private FocusPointStorage $storage) {}

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        if ($attachmentId === null) {
            return null;
        }

        return $this->storage->getManual($attachmentId);
    }
}