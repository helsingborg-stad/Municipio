<?php

namespace Municipio\PostObject\Decorators;

use AllowDynamicProperties;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;

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
        private SchemaObjectFromPostInterface $schemaObjectFromPost
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getSchemaProperty(string $property): mixed
    {
        if ($property === '@type') {
            return $this->getSchemaObject()->getType();
        }

        return $this->getSchemaObject()->getProperty($property);
    }

    /**
     * Get the schema object.
     *
     * @return BaseType
     */
    private function getSchemaObject(): BaseType
    {
        static $schemaObjectCache = [];
        $cacheKey                 = $this->postObject->getId();

        if (!isset($schemaObjectCache[$cacheKey])) {
            $schemaObjectCache[$cacheKey]   = $this->schemaObjectFromPost->create($this->postObject);
            $this->postObject->schemaObject = $schemaObjectCache[$cacheKey]; // TODO: remove when all usage of ->schemaObject is removed from the codebase.
        }

        return $schemaObjectCache[$cacheKey];
    }

    /**
     * @inheritDoc
     */
    public function __get(string $key): mixed
    {
        if ($key === 'schemaObject') {
            trigger_error('Deprecated: Use getSchemaObject() instead.', E_USER_DEPRECATED);
            return $this->getSchemaObject();
        }

        return $this->postObject->__get($key);
    }
}
