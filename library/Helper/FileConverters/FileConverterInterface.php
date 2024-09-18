<?php

namespace Municipio\Helper\FileConverters;

interface FileConverterInterface
{
    public static function convert(int $attachmentId): string;
}
