<?php

namespace Municipio\ExternalContent\Source;

use Spatie\SchemaOrg\Thing;

interface SchemaSourceReader
{
    public function getObject(string|int $id): ?Thing;

    /**
     * @param SchemaSourceFilter|null $filter
     * @return Thing[]
     */
    public function getObjects(?SchemaSourceFilter $filter = null): array;
};
