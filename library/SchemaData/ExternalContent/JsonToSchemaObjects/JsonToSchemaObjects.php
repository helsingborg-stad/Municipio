<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;

class JsonToSchemaObjects implements JsonToSchemaObjectsInterface
{
    /**
     * Transforms a JSON string into an array of schema objects.
     *
     * @param string $json
     * @return BaseType[]
     */
    public function transform(string $json): array
    {
        $decoded = json_decode($json, true);

        if (empty($decoded)) {
            return [];
        }

        return array_map(fn($item) => $this->generateSchemaObject($item), $this->ensureArrayOfSchemas($decoded));
    }

    private function ensureArrayOfSchemas($data): array
    {
        if( isset($data['@type']) ) {
            return [$data];
        }

        return is_array($data) ? $data : [$data];
    }

    /**
     * Returns a schema object based on the type string.
     *
     * @param string $type
     * @return BaseType
     */
    private function getSchemaObjectByType(string $type): BaseType
    {
        $typeName = str_replace('schema:', '', $type);
        return Schema::{lcfirst($typeName)}();
    }

    /**
     * Recursively generates a schema object from an associative array.
     *
     * @param array $jsonObject
     * @return BaseType
     */
    private function generateSchemaObject(array $jsonObject): BaseType
    {
        $object = $this->getSchemaObjectByType($jsonObject['@type']);

        foreach ($jsonObject as $key => $value) {
            $object->{$key}($this->convertValue($value));
        }

        return $object;
    }

    /**
     * Converts a value to its appropriate schema representation.
     *
     * @param mixed $value
     * @return mixed
     */
    private function convertValue($value)
    {
        if (is_array($value)) {
            if (isset($value['@type'])) {
                return $this->generateSchemaObject($value);
            }

            return array_map(function ($item) {
                return is_array($item) && isset($item['@type'])
                    ? $this->generateSchemaObject($item)
                    : $item;
            }, $value);
        }

        return $value;
    }
}