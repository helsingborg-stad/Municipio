<?php

namespace Municipio\ImageFocus\Resolvers;

use Municipio\ImageFocus\Resolvers\FocusPointResolverInterface;
use Imagick;

class AIFocusPointResolver implements FocusPointResolverInterface
{
    public function __construct(private $aiService) {}

    public function isSupported(): bool
    {
        return false;
    }

    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array
    {
        /** Not implemented. Example placeholder. */
        return null;
    }
}