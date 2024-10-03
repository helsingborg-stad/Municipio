<?php

namespace Municipio\ExternalContent\Sync\Utils;

interface DoingSync
{
    public function doingSync(string $postType): bool;
}
