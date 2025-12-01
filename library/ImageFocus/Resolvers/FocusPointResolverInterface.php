<?php

namespace Municipio\ImageFocus\Resolvers;

interface FocusPointResolverInterface
{
    public function isSupported(): bool;
    public function resolve(string $filePath, int $width, int $height, ?int $attachmentId = null): ?array;
}
