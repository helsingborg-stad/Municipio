<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper;

use AcfService\Contracts\GetFieldObject;

/**
 * Class FieldMapper
 *
 * Maps ACF fields to a name-value map.
 */
class FieldMapper implements FieldMapperInterface
{
    /**
     * Constructor.
     *
     * @param GetFieldObject $acfService
     */
    public function __construct(
        private GetFieldObject $acfService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getMappedFields(array $acfFields, array $postData): array
    {
        $nameKeyMap = $this->buildNameKeyMap($acfFields);
        return $this->buildNameValueMap($postData, $nameKeyMap);
    }

    /**
     * Build a name-key map from the ACF fields.
     *
     * @param array $subFields  The ACF fields.
     *
     * @return array The name-key map.
     */
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

    /**
     * Build a name-value map from the posted data and the name-key map.
     *
     * @param array $postedData  The posted data.
     * @param array $nameKeyMap  The name-key map.
     *
     * @return MappedFieldInterface[] The name-value map.
     */
    private function buildNameValueMap(array $postedData, array $nameKeyMap): array
    {
        $nameValueMap = [];

        foreach ($nameKeyMap as $name => $spec) {
            if (!empty($spec['sub_fields']) && $spec['is_repeater']) {
                $nameValueMap[$name] = new MappedField(
                    name: $name,
                    type: $spec['type'] ?? null,
                    value: array_values(array_map(
                        fn ($item) => $this->buildNameValueMap($item, $spec['sub_fields']),
                        $postedData[$spec['key']] ?: []
                    ))
                );
            } elseif (!empty($spec['sub_fields'])) {
                $nameValueMap[$name] = new MappedField(
                    name: $name,
                    type: $spec['type'] ?? null,
                    value: $this->buildNameValueMap($postedData[$spec['key']] ?? [], $spec['sub_fields']),
                );
            } else {
                $nameValueMap[$name] = new MappedField(
                    name: $name,
                    type: $spec['type'] ?? null,
                    value: $postedData[$spec['key']] ?? null,
                );
            }
        }

        return $nameValueMap;
    }
}
