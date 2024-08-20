<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

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
        $post            = new WP_Post((object) []);
        $post->post_type = 'post';

        $schemaObjectFromPost = new SchemaObjectFromPost($this->getUtil($schemaType));
        $schemaObject         = $schemaObjectFromPost->create($post);

        $this->assertInstanceOf(Thing::class, $schemaObject);
    }

    public function testReturnsThingIfSchemaTypeNotSet()
    {
        $post            = new WP_Post((object) []);
        $post->post_type = 'post';

        $schemaObjectFromPost = new SchemaObjectFromPost($this->getUtil(null));
        $schemaObject         = $schemaObjectFromPost->create($post);

        $this->assertInstanceOf(Thing::class, $schemaObject);
    }

    public function testReturnsMatchingSchemaTypeIfFound()
    {
        $schemaType      = 'Event';
        $post            = new WP_Post((object) []);
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
