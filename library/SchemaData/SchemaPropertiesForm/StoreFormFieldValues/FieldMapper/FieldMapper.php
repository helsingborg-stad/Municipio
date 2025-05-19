<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper;

use AcfService\Contracts\GetFieldObject;

class FieldMapper
{
    public function __construct(
        private GetFieldObject $acfService
    ) {
    }

    public function getMappedFields(array $acfFields, array $postData): array
    {
        $nameKeyMap = $this->buildNameKeyMap($acfFields);
        return $this->buildNameValueMap($postData, $nameKeyMap);
    }

    private function buildNameKeyMap(array $subFields): array
    {
        $nameKeyMap = [];

        foreach ($subFields as $subField) {
            if (isset($subField['sub_fields'])) {
                $nameKeyMap[$subField['name']] = [
                    'key'         => $subField['key'],
                    'name'        => $subField['name'],
                    'sub_fields'  => $this->buildNameKeyMap($subField['sub_fields']),
                    'is_repeater' => $subField['type'] === 'repeater',
                    'type'        => $this->acfService->getFieldObject($subField['key'])['type'] ?? null
                ];
            } else {
                $nameKeyMap[$subField['name']] = [
                    'key'  => $subField['key'],
                    'name' => $subField['name'],
                    'type' => $this->acfService->getFieldObject($subField['key'])['type'] ?? null
                ];
            }
        }

        return $nameKeyMap;
    }

    private function buildNameValueMap(array $postedData, array $nameKeyMap): array
    {
        $nameValueMap = [];

        foreach ($nameKeyMap as $name => $spec) {
            if (!empty($spec['sub_fields']) && $spec['is_repeater']) {
                $nameValueMap[$name] = [
                    'value' => array_values(array_map(
                        fn ($item) => $this->buildNameValueMap($item, $spec['sub_fields']),
                        $postedData[$spec['key']] ?: []
                    )),
                    'type'  => $spec['type'] ?? null,
                    'name'  => $name,
                ];
            } elseif (!empty($spec['sub_fields'])) {
                $nameValueMap[$name] = [
                    'value' => $this->buildNameValueMap($postedData[$spec['key']] ?? [], $spec['sub_fields']),
                    'type'  => $spec['type'] ?? null,
                    'name'  => $name,
                ];
            } else {
                $nameValueMap[$name] = [
                    'value' => $postedData[$spec['key']] ?? null,
                    'type'  => $spec['type'] ?? null,
                    'name'  => $name,
                ];
            }
        }

        return $nameValueMap;
    }
}
