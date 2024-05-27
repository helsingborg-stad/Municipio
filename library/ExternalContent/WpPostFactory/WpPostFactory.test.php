<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\ISource;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;
use WP_Post;
use WP_Query;

class WpPostFactoryTest extends TestCase
{
    /**
     * @testdox WP_Post is created with title and content
     */
    public function testCreate()
    {
        $schemaObject  = $this->getBaseTypeInstance([ 'name' => 'Title', 'description' => 'Content', ]);
        $wpPostFactory = new WpPostFactory();

        $wpPost = $wpPostFactory->create($schemaObject, $this->getNullSource());

        $this->assertInstanceOf(WP_Post::class, $wpPost);
        $this->assertEquals('Title', $wpPost->post_title);
        $this->assertEquals('Content', $wpPost->post_content);
    }

    /**
     * @testdox WP_Post is created with post_status publish
     */
    public function testCreateWithPublishStatus()
    {
        $schemaObject  = $this->getBaseTypeInstance();
        $source        = $this->getNullSource();
        $wpPostFactory = new WpPostFactory();

        $wpPost = $wpPostFactory->create($schemaObject, $this->getNullSource());

        $this->assertEquals('publish', $wpPost->post_status);
    }

    private function getBaseTypeInstance(array $properties = []): BaseType
    {
        return new class ($properties) extends BaseType {
            public function __construct(array $properties)
            {
                $this->properties = $properties;
            }
        };
    }

    private function getNullSource(): ISource
    {
        return new class implements ISource {
            public function getObject(string|int $id): null|BaseType
            {
                return null;
            }
            public function getObjects(?WP_Query $query = null): array
            {
                return [];
            }
            public function getPostType(): string
            {
                return '';
            }
            public function getId(): int
            {
                return 0;
            }
        };
    }
}
