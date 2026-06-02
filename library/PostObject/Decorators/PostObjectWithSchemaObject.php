<?php

declare(strict_types=1);


namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;

/**
 * Decorator for PostObject that adds schema object functionality.
 *
 * @package Municipio\PostObject\Decorators
 */
class PostObjectWithSchemaObject extends AbstractPostObjectDecorator implements PostObjectInterface
{
    private ?BaseType $schemaObject = null;

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
        if ($this->schemaObject === null) {
            $this->schemaObject = $this->schemaObjectFromPost->create($this->postObject);
        }

        return $this->schemaObject;
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
