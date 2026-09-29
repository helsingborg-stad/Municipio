<?php

namespace Municipio\Helper;

interface CacheBustInterface
{
    public function getManifest(): ?array;

    public function name(string $name): string;
}
