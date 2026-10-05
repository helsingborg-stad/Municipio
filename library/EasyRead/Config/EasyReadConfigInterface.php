<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Config;

interface EasyReadConfigInterface
{
    public function enabledField(): string;

    public function contentField(): string;

    public function postTypesField(): string;

    public function readableQueryParameter(): string;

    /** @return string[] */
    public function enabledPostTypes(): array;
}
