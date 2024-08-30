<?php

namespace Municipio\SchemaData\AcfFieldContentModifiers;

use Municipio\AcfFieldContentModifiers\AcfFieldContentModifierInterface;
use WpService\Contracts\ApplyFilters;

class PopulateSchemaTypeFieldOptions implements AcfFieldContentModifierInterface
{
    public function __construct(private string $fieldKey, private array $options, private ApplyFilters $wpService)
    {
    }

    public function modifyFieldContent(array $field): array
    {
        asort($this->options, SORT_NATURAL | SORT_FLAG_CASE);
        $field['choices'] = array('' => 'None') + $this->options;
        return $field;
    }

    public function getFieldKey(): string
    {
        return $this->fieldKey;
    }
}
