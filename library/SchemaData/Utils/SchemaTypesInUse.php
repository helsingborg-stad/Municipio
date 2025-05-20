<?php

namespace Municipio\SchemaData\Utils;

use Municipio\SchemaData\Utils\Contracts\SchemaTypesInUseInterface;
use wpdb;

/**
 * SchemaTypesInUse.
 *
 * Class to get all schema types in use.
 * A schema type in use is a schema type that is connected to an existing post type.
 */
class SchemaTypesInUse implements SchemaTypesInUseInterface
{
    public const SCHEMA_TYPE_OPTION_NAME = 'options_post_type_schema_types_%_schema_type';

    /**
     * SchemaTypesInUse constructor.
     *
     * @param wpdb $wpdb
     */
    public function __construct(private wpdb $wpdb)
    {
    }

    /**
     * @inheritDoc
     */
    public function getSchemaTypesInUse(): array
    {
        return $this->getRowsFromDb();
    }

    /**
     * Get all schema types in use.
     * A schema type in use is a schema type that is connected to an existing post type.
     *
     * @return string[]
     */
    private function getRowsFromDb(): array
    {
        return $this->wpdb->get_col($this->getQuery());
    }

    /**
     * Get the query to get all schema types in use.
     *
     * @return string
     */
    private function getQuery(): string
    {
        return $this->wpdb->prepare("SELECT option_value FROM {$this->wpdb->options} WHERE option_name LIKE %s", self::SCHEMA_TYPE_OPTION_NAME) ?? '';
    }
}
