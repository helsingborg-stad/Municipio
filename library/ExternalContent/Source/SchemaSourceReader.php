<?php

namespace Municipio\ExternalContent\Source;

use Spatie\SchemaOrg\Thing;

interface SchemaSourceReader
{
    public function getObject(string|int $id): ?object;

    /**
     * @param SchemaSourceFilter|null $filter
     * @return object[]
     */
    public function getObjects(?SchemaSourceFilter $filter = null): array;
};
