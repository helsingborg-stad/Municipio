<?php

namespace Municipio\PostTypeDesign;

/**
 * Class GetFields
 */
class GetFields implements GetFieldsInterface
{
    private array $fields      = [];
    private array $fieldKeys   = [];
    private array $filterables = [
        'colors'    => ['key' => 'type', 'value' => ['multicolor', 'color', 'background']],
        'logotypes' => ['key' => 'section', 'value' => ['municipio_customizer_section_logo']],
    ];

    /**
     * GetFields constructor.
     *
     * @param array $allFields An array of all fields.
     */
    public function __construct(private array $allFields = [])
    {
    }

    /**
     * Get the fields.
     *
     * @return array The fields.
     */
    public function getFields(?array $filter = null): array
    {
        $this->fields = !empty($this->fields) ? $this->fields : $this->sanitizeFields();

        if (!empty($filter)) {
            return array_filter($this->fields, function ($field) use ($filter) {
                return $this->isWantedField($field, $filter);
            });
        }

        return $this->fields;
    }

    /**
     * Get the field keys.
     *
     * @return array The field keys.
     */
    public function getFieldKeys(?array $filter = null): array
    {
        $this->fieldKeys = !empty($this->fieldKeys) ? $this->fieldKeys : $this->sanitizeFieldKeys($this->getFields());

        if (!empty($filter)) {
            return $this->sanitizeFieldKeys($this->getFields($filter));
        }

        return $this->fieldKeys;
    }

    /**
     * Sanitize the field keys.
     *
     * @return array The sanitized field keys.
     */
    private function sanitizeFieldKeys(array $fields): array
    {
        return array_map(function ($field) {
            return $field['settings'];
        }, $fields);
    }

    /**
     * Sanitize the fields.
     *
     * @return array The sanitized fields.
     */
    private function sanitizeFields(): array
    {
        $sanitizedFields = [];

        if (empty($this->allFields)) {
            return $sanitizedFields;
        }

        foreach ($this->allFields as $field) {
            if ($this->isWantedField($field)) {
                $sanitizedFields[] = $field;
            }
        }

        return $sanitizedFields;
    }

    /**
     * Checks if a field matches the desired criteria.
     *
     * @param array $field The field to check.
     * @param array $wantedFields The desired fields to match against.
     * @return bool Returns true if the field matches the desired criteria, false otherwise.
     */
    private function isWantedField($field, ?array $filter = null)
    {
        $filterables = $this->filterables;

        if (!empty($filter) && count($filter) !== count($filterables)) {
            foreach ($filterables as $filterKey => $value) {
                if (!in_array($filterKey, $filter)) {
                    unset($filterables[$filterKey]);
                }
            }
        }
        foreach ($filterables as $filterable) {
            if (empty($filterable)) {
                continue;
            }

            foreach ($filterable['value'] as $value) {
                if (isset($field[$filterable['key']]) && $field[$filterable['key']] === $value) {
                    return true;
                }
            }
        }

        return false;
    }
}
