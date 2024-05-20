<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost\Types;

use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Thing;

class SchemaObjectToWpPostTest extends TestCase
{
    /**
     * @testdox sets post title and content
     */
    public function testSuccess()
    {
        $schemaObject = new Thing();
        $schemaObject->identifier('1');
        $schemaObject->name('Title');
        $schemaObject->description('Description');
        $transformer = new Thing($schemaObject);

        $post = $transformer->toWpPost();

        $this->assertEquals('1', $post->ID);
        $this->assertEquals('Title', $post->post_title);
        $this->assertEquals('Description', $post->post_content);
    }
}
