<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Thing;
use WP_Post;

class SchemaObjectWithNameFromTitleTest extends TestCase
{
    public function testSetsTitleFromPostTitle()
    {
        $post                          = new WP_Post([]);
        $post->post_title              = 'Title';
        $schemaObjectWithNameFromTitle = new SchemaObjectWithNameFromTitle($this->getInner());
        $schemaObject                  = $schemaObjectWithNameFromTitle->create($post);

        $this->assertEquals('Title', $schemaObject['name']);
    }

    private function getInner(): SchemaObjectFromPostInterface
    {
        return new class implements SchemaObjectFromPostInterface {
            public function create(WP_Post $post): BaseType
            {
                return new Thing();
            }
        };
    }
}
