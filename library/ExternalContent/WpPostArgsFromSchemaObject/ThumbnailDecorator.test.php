<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\Source;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostFactory;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\ImageObject;
use WP_Error;
use WpService\Contracts\GetPosts;
use WpService\Contracts\MediaSideloadImage;

class ThumbnailDecoratorTest extends TestCase
{
    /**
     * @testdox Sideloads image from url
     */
    public function testSideloadImageFromUrl(): void
    {
        $url          = 'https://example.com/image.jpg';
        $wpService    = $this->getWpService();
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('image', $url);
        $decorator = new ThumbnailDecorator(new WpPostFactory(), $wpService);

        $post = $decorator->create($schemaObject, new Source('', ''));

        $this->assertEquals($url, $wpService->calls['mediaSideloadImage'][0][0]);
        $this->assertEquals(1, $post['meta_input']['_thumbnail_id']);
    }

    /**
     * @testdox Sideloads image from ImageObject
     */
    public function testSideloadImageFromImageObject(): void
    {
        $url           = 'https://example.com/image.jpg';
        $imageProperty = new ImageObject();
        $imageProperty->url($url);
        $wpService    = $this->getWpService();
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('image', $imageProperty);
        $decorator = new ThumbnailDecorator(new WpPostFactory(), $wpService);

        $post = $decorator->create($schemaObject, new Source('', ''));

        $this->assertEquals($url, $wpService->calls['mediaSideloadImage'][0][0]);
        $this->assertEquals(1, $post['meta_input']['_thumbnail_id']);
    }

    /**
     * @testdox Uses already uploaded image if found by source
     */
    public function testUsesAlreadyUploadedImageIfFound(): void
    {
        $url          = 'https://example.com/image.jpg';
        $wpService    = $this->getWpService(['getPosts' => [(object)['ID' => 123]]]);
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('image', $url);
        $decorator = new ThumbnailDecorator(new WpPostFactory(), $wpService);

        $post = $decorator->create($schemaObject, new Source('', ''));

        $this->assertEmpty($wpService->calls['mediaSideloadImage']);
        $this->assertEquals(123, $post['meta_input']['_thumbnail_id']);
    }

    private function getWpService(array $db = []): MediaSideloadImage&GetPosts
    {
        return new class ($db) implements MediaSideloadImage, GetPosts {
            public array $calls = ['mediaSideloadImage' => []];
            public function __construct(private array $db)
            {
            }

            public function mediaSideloadImage($file, $postId = 0, $desc = null, $returnType = 'html'): string|int|WP_Error
            {
                $this->calls['mediaSideloadImage'][] = func_get_args();
                return 1;
            }

            public function getPosts(array $args): array
            {
                return $this->db['getPosts'] ?? [];
            }
        };
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
        };
    }
}
