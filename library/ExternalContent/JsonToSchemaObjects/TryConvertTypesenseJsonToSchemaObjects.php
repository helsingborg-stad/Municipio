<?php

namespace Municipio\ExternalContent\JsonToSchemaObjects;

class TryConvertTypesenseJsonToSchemaObjects implements JsonToSchemaObjects {
    
    public function __construct(private JsonToSchemaObjects $inner = new SimpleJsonConverter())
    {
    }

    public function transform(string $json): array
    {
        $decoded = json_decode($json, true);

        if (empty($decoded) || !isset($decoded['hits'])) {
            return $this->inner->transform($json);
        }

        $documents = array_map(fn($hit) => $hit['document'] ?? null, $decoded['hits']);
        $documents = array_filter($documents);
        $documents = array_values($documents); // Reset keys after filter.

        return $this->inner->transform(json_encode($documents));
    }
}