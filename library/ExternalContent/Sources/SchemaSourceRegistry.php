<?php

namespace Municipio\ExternalContent\Sources;

use Municipio\ExternalContent\Sources\ISchemaSource;

class SchemaSourceRegistry implements ISchemaSourceRegistry
{
    private const ID_RANGE_START = 100;
    private array $sources       = [];

    public function registerSource(ISchemaSource $source): void
    {
        $this->sources[$this->getNextId()] = $source;
    }

    private function getNextId(): string
    {
        $nextId  = count($this->sources);
        $nextId += self::ID_RANGE_START;
        $nextId  = $nextId * -1;
        return (string)$nextId++;
    }

    /**
     * @inheritDoc
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @inheritDoc
     */
    public function getSourceById(string $id): ISchemaSource
    {
        return $this->sources[$id];
    }
}
