<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Mockery;
use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Thing;
use WP_Post;

class SchemaObjectWithNameFromTitleTest extends TestCase
{
    public function testSetsTitleFromPostTitle()
    {
        $post                          = WpMockFactory::createWpPost();
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
