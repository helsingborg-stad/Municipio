<?php

namespace Municipio\ImageFocus\Resolver;

use Imagick;

class AIFocusPointResolver implements FocusPointResolverInterface
{
    public function __construct(private $aiService) {}

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        /** Not implemented. Example placeholder. */
        return null;
    }
}