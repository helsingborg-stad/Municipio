<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\Thing;
use WP_Query;

class SourceService implements ISource
{
    private const ID_RANGE_START      = 100;
    private static int $instanceCount = 0;
    private int $id;

    public function __construct(private string $postType, private string $schemaType)
    {
        self::$instanceCount++;
        $this->id = self::$instanceCount + self::ID_RANGE_START;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getObject(string|int $id): null|Thing|Event
    {
        return null;
    }

    public function getObjects(?WP_Query $query = null): array
    {
        return [];
    }

    public function getPostType(): string
    {
        return $this->postType;
    }

    public function getType(): string
    {
        return $this->schemaType;
    }
}
