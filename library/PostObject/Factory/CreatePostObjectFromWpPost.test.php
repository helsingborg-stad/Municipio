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
        $post       = new \WP_Post([]);
        $post->ID   = 1;
        $factory    = $this->getInstance();
        $postObject = $factory->create($post);

        $this->assertInstanceOf(PostObjectInterface::class, $postObject);
    }

    private function getWpService(): WpService
    {
        return new FakeWpService(['isMultisite' => false]);
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
