<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Request;

use Municipio\EasyRead\Config\EasyReadConfigInterface;

final class NativeReadableRequest implements ReadableRequestInterface
{
    public function __construct(private EasyReadConfigInterface $config) {}

    public function isReadable(): bool
    {
        return ($_GET[$this->config->readableQueryParameter()] ?? null) === '1';
    }
}
