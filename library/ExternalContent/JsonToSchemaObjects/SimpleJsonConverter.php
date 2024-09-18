<?php

namespace Municipio\ExternalContent\JsonToSchemaObjects;

use Spatie\SchemaOrg\Schema;

class SimpleJsonConverter implements JsonToSchemaObjects {
    
    /**
     * @inheritDoc
     */
    public function transform(string $json): array
    {
        $decoded = json_decode($json, true);

        if( empty($decoded) ) {
            return [];
        }

        $objects = array_map([$this, 'generateSchemaObject'], $decoded);

        return $objects;
    }

    private function typeFromString(string $type): object
    {
        return Schema::{lcfirst($type)}();
    }

    private function generateSchemaObject(array $jsonObject): object
    {
        $object = $this->typeFromString($jsonObject['@type']);
            
        foreach($jsonObject as $key => $value) {

            if( is_array($value) && isset($value['@type']) ) {
                $value = $this->generateSchemaObject($value);
            } else if(is_array($value)) {
                $convertedValue = [];
                foreach($value as $index => $item) {
                    if( is_array($item) && isset($item['@type']) ) {
                        $convertedValue[$index] = $this->generateSchemaObject($item);
                    }
                }
                $value = $convertedValue;
            }

            $object->{$key}($value);
        }

        return $object;
    }

}