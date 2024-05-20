<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost;

use Spatie\SchemaOrg\BaseType;
use WP_Post;

class SchemaObjectToWpPost implements ISchemaObjectToWpPost
{
    public function __construct(private BaseType $schemaObject)
    {
    }

    public function toWpPost(): WP_Post
    {
        $transformerClass = $this->getTransFormerClassInstance();
        return $transformerClass->toWpPost();
    }

    private function getTransFormerClassInstance(): ISchemaObjectToWpPost
    {
        $type             = $this->schemaObject->getType();
        $transformerClass = 'Municipio\ExternalContent\SchemaObjectToWpPost\Types\\' . ucfirst($type);
        return new $transformerClass($this->schemaObject);
    }
}
