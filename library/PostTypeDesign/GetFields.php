<?php

namespace Municipio\PostTypeDesign;

class GetFields implements GetFieldsInterface
{
    private array $fields    = [];
    private array $fieldKeys = [];

    public function __construct(private array $allFields = [])
    {
    }

    public function getFields(): array
    {
        if (!empty($this->fields)) {
            return $this->fields;
        }

        return $this->sanitizeFields();
    }

    public function getFieldKeys(): array
    {
        if (!empty($this->fieldKeys)) {
            return $this->fieldKeys;
        }

        return $this->sanitizeFieldKeys();
    }

    private function sanitizeFieldKeys(): array
    {
        $fields = !empty($this->fields) ? $this->fields : $this->sanitizeFields();

        return array_map(function ($field) {
            return $field['settings'];
        }, $fields);
    }

    private function sanitizeFields(): array
    {
        $sanitizedFields = [];
        $colorFieldTypes = ['multicolor', 'color', 'background'];

        foreach ($colorFieldTypes as $colorFieldType) {
            $sanitizedFields = array_merge(array_filter($this->allFields, function ($field) use ($colorFieldType) {
                return $field['type'] === $colorFieldType;
            }), $sanitizedFields);
        }

        return $sanitizedFields;
    }
}
