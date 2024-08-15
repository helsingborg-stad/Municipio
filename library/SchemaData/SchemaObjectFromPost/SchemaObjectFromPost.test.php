<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Mockery;
use Municipio\SchemaData\Utils\GetSchemaTypeFromPostTypeInterface;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\Thing;
use WP_Post;

class SchemaObjectFromPostTest extends TestCase
{
    public function testReturnsThingIfInvalidSchemaType()
    {
        $schemaType      = 'NonExistingSchemaType';
        $post            = Mockery::mock(WP_Post::class);
        $post->post_type = 'post';

        $schemaObjectFromPost = new SchemaObjectFromPost($this->getUtil($schemaType));
        $schemaObject         = $schemaObjectFromPost->create($post);

        $this->assertInstanceOf(Thing::class, $schemaObject);
    }

    public function testReturnsThingIfSchemaTypeNotSet()
    {
        $post            = Mockery::mock(WP_Post::class);
        $post->post_type = 'post';

        $schemaObjectFromPost = new SchemaObjectFromPost($this->getUtil(null));
        $schemaObject         = $schemaObjectFromPost->create($post);

        $this->assertInstanceOf(Thing::class, $schemaObject);
    }

    public function testReturnsMatchingSchemaTypeIfFound()
    {
        $schemaType      = 'Event';
        $post            = Mockery::mock(WP_Post::class);
        $post->post_type = 'post';

        $schemaObjectFromPost = new SchemaObjectFromPost($this->getUtil($schemaType));
        $schemaObject         = $schemaObjectFromPost->create($post);

        $this->assertInstanceOf(Event::class, $schemaObject);
    }

    private function getUtil(?string $schemaType = ''): GetSchemaTypeFromPostTypeInterface
    {
        return new class ($schemaType) implements GetSchemaTypeFromPostTypeInterface {
            public function __construct(private ?string $schemaType)
            {
            }

            public function getSchemaTypeFromPostType(string $postType): ?string
            {
                return $this->schemaType;
            }
        };
    }
}
