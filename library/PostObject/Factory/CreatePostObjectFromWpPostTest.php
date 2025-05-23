<?php

namespace Municipio\PostObject\Factory;

use AcfService\AcfService;
use AcfService\Implementations\FakeAcfService;
use Municipio\PostObject\PostObjectInterface;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

class CreatePostObjectFromWpPostTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(CreatePostObjectFromWpPost::class, $this->getInstance());
    }

    /**
     * @testdox create() method returns a PostObjectInterface
     */
    public function testCreateMethodReturnsPostObjectInterface(): void
    {
        $post       = $this->getWpPost();
        $factory    = $this->getInstance();
        $postObject = $factory->create($post);

        $this->assertInstanceOf(PostObjectInterface::class, $postObject);
    }

    /**
     * @testdox create() method applies filter that allows for adding custom decorators
     */
    public function testCreateMethodAppliesFilterForCustomDecorators(): void
    {
        $post             = $this->getWpPost();
        $customPostObject = $this->createMock(PostObjectInterface::class);
        $customPostObject->method('getTitle')->willReturn('custom_post_object');
        $factory    = new CreatePostObjectFromWpPost(
            $this->getWpService(['applyFilters' => fn($hook, $postObject) => $hook === CreatePostObjectFromWpPost::DECORATE_FILTER_NAME ? $customPostObject : $postObject]),
            $this->getAcfService(),
            $this->getSchemaObjectFromPost()
        );

        $postObject = $factory->create($post);

        $this->assertEquals('custom_post_object', $postObject->getTitle());
    }

    private function getWpPost(): \WP_Post
    {
        $post     = new \WP_Post([]);
        $post->ID = 1;

        return $post;
    }

    private function getWpService(array $args = []): WpService
    {
        return new FakeWpService(array_merge([
            'applyFilters' => fn($hook, $postObject) => $postObject,
        ], $args));
    }

    private function getAcfService(): AcfService
    {
        return new FakeAcfService();
    }

    private function getSchemaObjectFromPost(): SchemaObjectFromPostInterface|MockObject
    {
        return $this->createMock(SchemaObjectFromPostInterface::class);
    }

    private function getInstance(): CreatePostObjectFromWpPost
    {
        return new CreatePostObjectFromWpPost(
            $this->getWpService(),
            $this->getAcfService(),
            $this->getSchemaObjectFromPost()
        );
    }
}
