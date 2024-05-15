<?php

namespace Municipio\ExternalContent\JsonToSchemaObjects;

class SimpleJsonConverter implements JsonToSchemaObjects {
    
    public function transform(string $json): array
    {
        $json = json_decode($json, true);
        $objects = array_map([$this, 'generateSchemaObject'], $json);

        return $objects;
    }

    private function typeFromString(string $type)
    {
        $className = 'Spatie\SchemaOrg\\' . ucfirst($type);
        return new $className();
    }

    private function generateSchemaObject(array $jsonObject): object
    {
        $object = $this->typeFromString($jsonObject['@type']);
            
        foreach($jsonObject as $key => $value) {

            if( is_array($value) && isset($value['@type']) ) {
                $value = $this->generateSchemaObject($value);
            }

            $object->{$key}($value);
        }

        return $object;
    }

}