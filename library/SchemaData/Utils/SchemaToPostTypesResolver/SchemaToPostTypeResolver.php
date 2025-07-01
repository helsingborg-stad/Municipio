<?php

namespace Municipio\SchemaData\Utils\SchemaToPostTypesResolver;

use AcfService\Contracts\GetField;
use WpService\Contracts\PostTypeExists;

class SchemaToPostTypeResolver implements SchemaToPostTypeResolverInterface
{
    public function __construct(private GetField $acfService, private PostTypeExists $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $schemaType): array
    {
        $postTypes = [];

        foreach ($this->acfService->getField('post_type_schema_types', 'option') ?: [] as $row) {
            if ($this->tryGetSchemaTypeFromRow($row) === $schemaType) {
                $postTypes[] = $row['post_type'];
            }
        }

        return $postTypes;
    }

    /**
     * Try to get the schema type from a row.
     *
     * @param array $row The row to check.
     * @return string|null The schema type if found, null otherwise.
     */
    private function tryGetSchemaTypeFromRow(array $row): ?string
    {
        return $this->isValidRow($row) ? $row['schema_type'] : null;
    }

    /**
     * Check if the row is valid.
     *
     * @param array $row The row to check.
     * @return bool True if the row is valid, false otherwise.
     */
    private function isValidRow(array $row): bool
    {
        return
            isset($row['post_type'], $row['schema_type']) &&
            is_string($row['post_type']) &&
            is_string($row['schema_type']) &&
            $this->wpService->postTypeExists($row['post_type']);
    }
}
