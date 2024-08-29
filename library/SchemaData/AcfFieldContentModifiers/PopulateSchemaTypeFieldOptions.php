<?php

namespace Municipio\SchemaData\AcfFieldContentModifiers;

use Municipio\AcfFieldContentModifiers\AcfFieldContentModifierInterface;
use WpService\Contracts\ApplyFilters;

class PopulateSchemaTypeFieldOptions implements AcfFieldContentModifierInterface
{
    public function __construct(private string $fieldKey, private array $schemaTypes, private ApplyFilters $wpService)
    {
    }

    public function modifyFieldContent(array $field): array
    {
        $schemaTypes = $this->wpService->applyFilters('Municipio/SchemaData/SchemaTypes', $this->schemaTypes);

        foreach ($schemaTypes as $type) {
            $options[$type] = $type;
        }

        asort($options, SORT_NATURAL | SORT_FLAG_CASE);
        $field['choices'] = array('' => 'None') + $options;
        return $field;
    }

    public function getFieldKey(): string
    {
        return $this->fieldKey;
    }
}
