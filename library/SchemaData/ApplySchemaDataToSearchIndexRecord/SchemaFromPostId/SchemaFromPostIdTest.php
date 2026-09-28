<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord\SchemaFromPostId;

use Municipio\PostObject\Factory\PostObjectFromWpPostFactoryInterface;
use Municipio\PostObject\NullPostObject;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Post;
use WpService\Contracts\GetPost;

class SchemaFromPostIdTest extends TestCase
{
    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $schemaFromPostId = new SchemaFromPostId(static::createWpService(), static::createPostObjectFactory(Schema::thing()));

        static::assertInstanceOf(SchemaFromPostId::class, $schemaFromPostId);
    }

    #[TestDox('returns the schema of the post object created from the post id')]
    public function testReturnsSchemaOfPostObject(): void
    {
        $schema = Schema::event();
        $schemaFromPostId = new SchemaFromPostId(static::createWpService(), static::createPostObjectFactory($schema));

        $result = $schemaFromPostId->getSchema(123);

        static::assertSame($schema, $result);
    }

    private static function createWpService(): GetPost
    {
        return new class implements GetPost {
            public function getPost(int|WP_Post|null $post = null, string $output = OBJECT, string $filter = 'raw'): WP_Post|array|null
            {
                return new WP_Post([]);
            }
        };
    }

    private static function createPostObjectFactory(BaseType $schema): PostObjectFromWpPostFactoryInterface
    {
        return new class($schema) implements PostObjectFromWpPostFactoryInterface {
            public function __construct(
                private BaseType $schema,
            ) {}

            public function create(WP_Post $post): PostObjectInterface
            {
                return new class($this->schema) extends NullPostObject {
                    public function __construct(
                        private BaseType $schema,
                    ) {}

                    public function getSchema(): BaseType
                    {
                        return $this->schema;
                    }
                };
            }
        };
    }
}
