<?php

namespace Modularity\Module\Posts\TemplateController;

use Modularity\Helper\WpService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WP_Post;
use WpService\Implementations\FakeWpService;

class AbstractControllerTest extends TestCase
{
    protected function setUp(): void
    {
        WpService::set(new FakeWpService([
        ]));
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $controller = new AbstractController($this->getModuleMock());
        $this->assertInstanceOf(AbstractController::class, $controller);
    }

    #[TestDox('postUsesSchemaTypeEvent() returns true if schema @type is Event')]
    public function testPostUsesSchemaTypeEventReturnsTrue()
    {
        $controller = new AbstractController($this->getModuleMock());
        $post       = $this->getPostObjectFake([ 'getSchemaProperty' => fn($property) => $property === '@type' ? 'Event' : null, ]);

        $this->assertTrue($controller->postUsesSchemaTypeEvent($post));
    }

    #[TestDox('postUsesSchemaTypeEvent() returns false if schema @type is not Event')]
    public function testPostUsesSchemaTypeEventReturnsFalse()
    {
        $controller = new AbstractController($this->getModuleMock());
        $post       = $this->getPostObjectFake([ 'getSchemaProperty' => fn($property) => $property === '@type' ? 'Article' : null, ]);

        $this->assertFalse($controller->postUsesSchemaTypeEvent($post));
    }

    #[TestDox('shouldAddBlogNameToPost() returns true if blog name should be added')]
    public function testShouldAddBlogNameToPostReturnsTrue()
    {
        $controller           = new AbstractController($this->getModuleMock());
        $post                 = new WP_Post([]);
        $post->originalBlogId = 2;

        $this->assertTrue($controller->shouldAddBlogNameToPost($post));
    }

    #[TestDox('shouldAddBlogNameToPost() returns true if force is provided as true')]
    public function testShouldAddBlogNameToPostReturnsTrueIfForce()
    {
        $controller = new AbstractController($this->getModuleMock());
        $post       = new WP_Post([]);

        $this->assertTrue($controller->shouldAddBlogNameToPost($post, true));
    }

    #[TestDox('shouldAddBlogNameToPost() returns false if blog name should not be added')]
    public function testShouldAddBlogNameToPostReturnsFalse()
    {
        $controller = new AbstractController($this->getModuleMock());
        $post       = new WP_Post([]);

        $this->assertFalse($controller->shouldAddBlogNameToPost($post));
    }

    private function getModuleMock(): \Modularity\Module\Posts\Posts|MockObject
    {
        $module                = $this->createMock(\Modularity\Module\Posts\Posts::class);
        $module->data          = ['posts' => []];
        $module->fields        = ['posts_columns' => 2];
        $module->domainChecker = $this->createMock(\Modularity\Module\Posts\Helper\DomainChecker::class);

        return $module;
    }

    private function getPostObjectFake(array $returns): object
    {
        return new class ($returns) {
            public function __construct(private array $returns)
            {
            }
            public function getSchemaProperty($property)
            {
                return $this->returns['getSchemaProperty']($property);
            }
        };
    }
}
