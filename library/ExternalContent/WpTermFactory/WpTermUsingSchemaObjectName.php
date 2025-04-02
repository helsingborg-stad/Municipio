<?php

namespace Municipio\ExternalContent\WpTermFactory;

use Municipio\Schema\BaseType;
use WP_Term;

/**
 * Class WpTermUsingSchemaObjectName
 */
class WpTermUsingSchemaObjectName implements WpTermFactoryInterface
{
    /**
     * WpTermUsingSchemaObjectName constructor.
     *
     * @param WpTermFactoryInterface $inner
     */
    public function __construct(private WpTermFactoryInterface $inner)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(BaseType|string $schemaObject, string $taxonomy): WP_Term
    {
        $term = $this->inner->create($schemaObject, $taxonomy);

        // If schemaobject extends basetype, we can get the name from the schemaobject
        if ($schemaObject instanceof BaseType) {
            $name = $schemaObject['name'] ?? $term->name;

            if (is_string($name)) {
                $term->name = $schemaObject['name'] ?? $term->name;
            }
        }

        return $term;
    }
}
