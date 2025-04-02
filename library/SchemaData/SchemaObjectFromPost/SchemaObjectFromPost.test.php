<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\Event;
use Municipio\Schema\Thing;
use WP_Post;

class SchemaObjectFromPostTest extends TestCase
{
    public function testReturnsThingIfInvalidSchemaType()
    {
        $schemaType      = 'NonExistingSchemaType';
        $post            = new WP_Post([]);
        $post->post_type = 'post';

        $schemaObjectFromPost = new SchemaObjectFromPost($this->getUtil($schemaType));
        $schemaObject         = $schemaObjectFromPost->create($post);

        $this->assertInstanceOf(Thing::class, $schemaObject);
    }

    public function testReturnsThingIfSchemaTypeNotSet()
    {
        $post            = new WP_Post([]);
        $post->post_type = 'post';

        $schemaObjectFromPost = new SchemaObjectFromPost($this->getUtil(null));
        $schemaObject         = $schemaObjectFromPost->create($post);

        $this->assertInstanceOf(Thing::class, $schemaObject);
    }

    public function testReturnsMatchingSchemaTypeIfFound()
    {
        $schemaType      = 'Event';
        $post            = new WP_Post([]);
        $post->post_type = 'post';

        $schemaObjectFromPost = new SchemaObjectFromPost($this->getUtil($schemaType));
        $schemaObject         = $schemaObjectFromPost->create($post);

        $this->assertInstanceOf(Event::class, $schemaObject);
    }

    private function getUtil(?string $schemaType = ''): TryGetSchemaTypeFromPostType
    {
        return new class ($schemaType) implements TryGetSchemaTypeFromPostType {
            public function __construct(private ?string $schemaType)
            {
            }

            public function tryGetSchemaTypeFromPostType(string $postType): ?string
            {
                return $this->schemaType;
            }
        };
    }
}
