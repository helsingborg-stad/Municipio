<?php

namespace Municipio\SchemaData\Utils;

use Municipio\SchemaData\Utils\Contracts\SchemaTypesInUseInterface;
use wpdb;

class SchemaTypesInUse implements SchemaTypesInUseInterface
{
    public const SCHEMA_TYPE_OPTION_NAME = 'options_post_type_schema_types_%_schema_type';

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

    private function getRowsFromDb(): array
    {
        return $this->wpdb->get_col($this->getQuery());
    }

    private function getQuery(): string
    {
        return $this->wpdb->prepare("SELECT option_value FROM {$this->wpdb->options} WHERE option_name LIKE %s", self::SCHEMA_TYPE_OPTION_NAME);
    }
}
