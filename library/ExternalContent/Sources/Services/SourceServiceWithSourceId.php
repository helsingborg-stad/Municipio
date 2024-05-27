<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;
use WP_Query;

class SourceServiceWithSourceId implements ISource
{
    public static $idRegistry = [];

    public function __construct(private string $id, private ISource $inner = new NullSourceService())
    {
        self::$idRegistry[] = $this->id = $this->ensureIdIsUnique();
        ;
    }

    private function ensureIdIsUnique(): string
    {
        $this->id = md5($this->id);

        if (in_array($this->id, self::$idRegistry)) {
            return $this->ensureIdIsUnique();
        }

        return $this->id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getObject(string|int $id): null|BaseType
    {
        return $this->inner->getObject($id);
    }

    public function getObjects(?WP_Query $query = null): array
    {
        return $this->inner->getObjects($query);
    }

    public function getPostType(): string
    {
        return $this->inner->getPostType();
    }
}
