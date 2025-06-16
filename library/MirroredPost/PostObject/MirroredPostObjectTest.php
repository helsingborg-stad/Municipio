<?php

namespace Municipio\MirroredPost\PostObject;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\Schema;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\RestoreCurrentBlog;
use WpService\Contracts\SwitchToBlog;
use WpService\Implementations\FakeWpService;

class MirroredPostObjectTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated(): void
    {
        $decorator = $this->createMirroredPostObject(1);
        $this->assertInstanceOf(MirroredPostObject::class, $decorator);
    }

    /**
     * @testdox getBlogId returns the provided blog id
     */
    public function testGetBlogIdReturnsTheProvidedBlogId(): void
    {
        $decorator = $this->createMirroredPostObject(2);
        $this->assertEquals(2, $decorator->getBlogId());
    }

    /**
     * @testdox getPermalink returns the permalink with extra query vars indicating the blog id and the original post id.
     */
    public function testGetPermalinkAppendsBlogAndPostId(): void
    {
        $postObject = $this->createPostObjectStub([
            'getId'        => 123,
            'getPermalink' => 'http://example.com/hello-world/',
        ]);
        $decorator  = new MirroredPostObject($postObject, $this->createWpService(), 2);

        $permalink = $decorator->getPermalink();

        $this->assertStringContainsString(BlogIdQueryVar::BLOG_ID_QUERY_VAR . '=2', $permalink);
        $this->assertStringContainsString('p=123', $permalink);
    }

    /**
     * @testdox getPermalink replaces the original site url with the current site url
     */
    public function testGetPermalinkReplacesSiteUrl(): void
    {
        $postObject = $this->createPostObjectStub([
            'getPermalink' => 'http://other-site.com/hello-world/',
        ]);
        $wpService  = new FakeWpService([
            'switchToBlog'       => true,
            'restoreCurrentBlog' => true,
            'getSiteUrl'         => fn($blogId = null) => $blogId === 2 ? 'http://other-site.com' : 'http://current-site.com',
            'addQueryArg'        => fn($args, $url) => $url . '?' . http_build_query($args),
            'getCurrentBlogId'   => 1,
        ]);
        $decorator  = new MirroredPostObject($postObject, $wpService, 2);

        $permalink = $decorator->getPermalink();

        $this->assertStringContainsString('http://current-site.com/hello-world/', $permalink);
    }

    /**
     * @testdox getIcon() switches to the blog using the provided blog id when getting the value
     */
    public function testGetIconSwitchesToTheProvidedBlogIdWhenGettingTheValue(): void
    {
        $postObject = $this->createPostObjectStub(['getIcon' => null]);
        $wpService  = $this->createWpService();
        $decorator  = new MirroredPostObject($postObject, $wpService, 2);

        $icon = $decorator->getIcon();

        $this->assertEquals(2, $wpService->methodCalls['switchToBlog'][0][0]);
        $this->assertEquals(1, $wpService->methodCalls['switchToBlog'][1][0]);
        $this->assertNull($icon);
    }

    /**
     * @testdox getSchemaProperty() switches to the blog using the provided blog id when getting the value
     */
    public function testGetSchemaPropertySwitchesToTheProvidedBlogIdWhenGettingTheValue(): void
    {
        $postObject = $this->createPostObjectStub(['getSchemaProperty' => 'schema-value']);
        $wpService  = $this->createWpService();
        $decorator  = new MirroredPostObject($postObject, $wpService, 2);

        $schemaValue = $decorator->getSchemaProperty('some-property');

        $this->assertEquals(2, $wpService->methodCalls['switchToBlog'][0][0]);
        $this->assertEquals(1, $wpService->methodCalls['switchToBlog'][1][0]);
        $this->assertEquals('schema-value', $schemaValue);
    }

    /**
     * @testdox getSchema() switches to the blog using the provided blog id when getting the value
     */
    public function testGetSchemaSwitchesToTheProvidedBlogIdWhenGettingTheValue(): void
    {
        $schema     = Schema::thing();
        $postObject = $this->createPostObjectStub(['getSchema' => $schema]);
        $wpService  = $this->createWpService();
        $decorator  = new MirroredPostObject($postObject, $wpService, 2);

        $schemaData = $decorator->getSchema();

        $this->assertCount(2, $wpService->methodCalls['switchToBlog']);
        $this->assertEquals($schema, $schemaData);
    }

    /**
     * Helper to create a MirroredPostObject with a stubbed PostObject and WpService.
     */
    private function createMirroredPostObject(int $blogId): MirroredPostObject
    {
        return new MirroredPostObject(
            $this->createPostObjectStub(),
            $this->createWpService(),
            $blogId
        );
    }

    /**
     * Helper to create a stub for PostObjectInterface with optional method overrides.
     */
    private function createPostObjectStub(array $methods = []): PostObjectInterface|MockObject
    {
        $stub = $this->createStub(PostObjectInterface::class);
        foreach ($methods as $method => $return) {
            $stub->method($method)->willReturn($return);
        }
        return $stub;
    }

    /**
     * Helper to create a FakeWpService with default behaviors.
     */
    private function createWpService(): SwitchToBlog&RestoreCurrentBlog
    {
        return new FakeWpService([
            'switchToBlog'       => true,
            'restoreCurrentBlog' => true,
            'getSiteUrl'         => 'http://example.com',
            'addQueryArg'        => fn($args, $url) => $url . '?' . http_build_query($args),
            'getCurrentBlogId' => 1,
        ]);
    }
}
