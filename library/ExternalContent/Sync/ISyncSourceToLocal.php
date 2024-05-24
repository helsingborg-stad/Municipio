<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\ExternalContent\Sources\ISource;

interface ISyncSourceToLocal
{
    public function sync(ISource $source): void;
}
