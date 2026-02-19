<?php

namespace Municipio\PostObject\Decorators;

use AllowDynamicProperties;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use WpService\Contracts\ApplyFilters;

/**
 * Decorator for PostObject that adds schema object functionality.
 *
 * @package Municipio\PostObject\Decorators
 */
#[AllowDynamicProperties]
class PostObjectWithSchemaObject extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private SchemaObjectFromPostInterface $schemaObjectFromPost,
        private ApplyFilters $wpService,
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProperty(string $property): mixed
    {
        $type = $this->getSchema()->getType();
        if ($property === '@type') {
            return $type;
        }
        /**
         * Apply filters to allow modification of schema properties. The filter name includes the schema type and property name for specificity.
         * Example: Municipio/PostObject/Schema/Place/address
         */
        return $this->wpService->applyFilters(
            "Municipio/PostObject/Schema/{$type}/{$property}",
            $this->getSchema()->getProperty($property),
            $this->postObject,
        );
    }

    /**
     * Get the schema object.
     *
     * @return BaseType
     */
    public function getSchema(): BaseType
    {
        if (!isset($this->postObject->schemaObject)) {
            return @($this->postObject->schemaObject = $this->schemaObjectFromPost->create($this->postObject));
        }

        return $this->postObject->schemaObject;
    }

    /**
     * @inheritDoc
     */
    public function __get(string $key): mixed
    {
        if ($key === 'schemaObject') {
            trigger_error('Deprecated: Use getSchema() instead.', E_USER_DEPRECATED);
            return $this->getSchema();
        }

        return $this->postObject->__get($key);
    }
}
