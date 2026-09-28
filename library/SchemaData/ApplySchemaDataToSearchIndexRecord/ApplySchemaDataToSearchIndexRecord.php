<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord;

use Municipio\HooksRegistrar\Hookable;
use Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord\SchemaFromPostId\SchemaFromPostIdInterface;
use WpService\Contracts\AddFilter;

class ApplySchemaDataToSearchIndexRecord implements Hookable
{
    public function __construct(
        private AddFilter $wpService,
        private SchemaFromPostIdInterface $schemaFromPostId,
        private AllowedSchemaTypes $allowedSchemaTypesService = new AllowedSchemaTypes(),
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/SearchIndex/Record', [$this, 'apply'], 10, 2);
    }

    public function apply(array $record, int $postId): array
    {
        $schema = $this->schemaFromPostId->getSchema($postId);

        if (!$this->allowedSchemaTypesService->isAllowed($schema->getType())) {
            return $record;
        }

        $record['schema' . $schema->getType()] = $schema->toArray();

        return $record;
    }
}
