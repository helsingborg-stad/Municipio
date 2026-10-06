<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Content;

interface CurrentUrlInterface
{
    public function get(): string;
}
