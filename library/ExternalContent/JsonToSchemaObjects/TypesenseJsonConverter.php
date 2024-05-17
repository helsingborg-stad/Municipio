<?php

namespace Municipio\ExternalContent\JsonToSchemaObjects;

class TypesenseJsonConverter implements JsonToSchemaObjects {
    
    public function __construct(private JsonToSchemaObjects $inner = new SimpleJsonConverter())
    {
    }

    public function transform(string $json): array
    {
        $json = json_decode($json, true);

        if (empty($json) || !isset($json['hits'])) {
            return [];
        }

        $documents = array_map(fn($hit) => $hit['document'] ?? null, $json['hits']);
        $documents = array_filter($documents);
        $documents = array_values($documents); // Reset keys after filter.

        return $this->inner->transform(json_encode($documents));
    }
}