<?php

namespace Municipio\ImageFocus\Resolver;

interface FocusPointResolverInterface
{
    public function resolve(string $filePath, int $width, int $height, int $attachmentId = null): ?array;
}