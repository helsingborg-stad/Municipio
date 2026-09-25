<?php

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord;

use Municipio\HooksRegistrar\Hookable;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SearchIndex\Provider\Typesense\TypesenseProvider;
use WpService\Contracts\AddFilter;

class ApplySchemaTypesToTypesenseSchemaFields implements Hookable
{
    private const FIELD_PREFIX = 'schema';

    public function __construct(
        private AddFilter $wpService,
        private AllowedSchemaTypes $allowedSchemaTypesService = new AllowedSchemaTypes(),
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter(TypesenseProvider::SCHEMA_FIELDS_FILTER, [$this, 'apply']);
    }

    public function apply(array $fields): array
    {
        foreach ($this->allowedSchemaTypesService->getAllowedSchemaTypes() as $fieldSchemaType) {
            $fields = $this->addFieldForSchemaType($fields, $fieldSchemaType);
        }

        return $fields;
    }

    private function addFieldForSchemaType(array $fields, string $fieldSchemaType): array
    {
        $fields[] = [
            'name' => $this->getFieldNameFromSchemaName($fieldSchemaType),
            'type' => 'object',
            'optional' => true
        ];

        return $fields;
    }

    private function getFieldNameFromSchemaName(string $schemaName): string
    {
        return static::FIELD_PREFIX . $schemaName;
    }

    private function getSupportedSchemaTypes(): \Generator
    {
        yield Schema::elementarySchool();
        yield Schema::event();
        yield Schema::exhibitionEvent();
        yield Schema::jobPosting();
        yield Schema::place();
        yield Schema::preschool();
        yield Schema::project();
        yield Schema::thing();
    }
}
