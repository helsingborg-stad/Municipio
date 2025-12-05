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
            return $this->getSchema()->getType();
        }

        return $this->getSchema()->getProperty($property);
    }

    /**
     * Get the schema object.
     *
     * @return BaseType
     */
    public function getSchema(): BaseType
    {
        if (!isset($this->postObject->schemaObject)) {
            return @$this->postObject->schemaObject = $this->schemaObjectFromPost->create($this->postObject);
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
