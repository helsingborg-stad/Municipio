<?php

namespace Municipio\ExternalContent\Source;

use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\JobPosting;
use Spatie\SchemaOrg\Thing;

interface ISchemaSource
{
    public function getObject(string|int $id): null|Thing|Event|JobPosting;

    /**
     * @param SchemaSourceFilter|null $filter
     * @return (Thing|Event|JobPosting)[]
     */
    public function getObjects(?ISchemaSourceFilter $filter = null): array;
};
