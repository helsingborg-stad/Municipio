<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Request;

interface ReadableRequestInterface
{
    public function isReadable(): bool;
}
