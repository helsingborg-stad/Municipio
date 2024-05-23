<?php

namespace Municipio\ExternalContent\Sources;

use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\JobPosting;
use Spatie\SchemaOrg\Thing;

interface ISource
{
    public function getObject(string|int $id): null|Thing|Event|JobPosting;

    /**
     * @param SchemaSourceFilter|null $filter
     * @return (Thing|Event|JobPosting)[]
     */
    public function getObjects(?ISourceFilter $filter = null): array;
    public function getPostType(): string;
    public function getType(): string;
    public function getId(): int;
};
