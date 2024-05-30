<?php

namespace Municipio\ExternalContent\WpTermFactory;

use Spatie\SchemaOrg\BaseType;
use WP_Term;

class WpTermUsingSchemaObjectName implements WpTermFactoryInterface
{
    public function __construct(private WpTermFactoryInterface $inner)
    {
    }

    public function create(BaseType|string $schemaObject, string $taxonomy): WP_Term
    {
        $term = $this->inner->create($schemaObject, $taxonomy);

        // If schemaobject extends basetype, we can get the name from the schemaobject
        if ($schemaObject instanceof BaseType) {
            $term->name = $schemaObject['name'] ?? $term->name;
        }

        return $term;
    }
}
