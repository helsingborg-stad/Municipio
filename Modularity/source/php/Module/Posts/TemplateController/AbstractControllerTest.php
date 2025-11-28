<?php

declare(strict_types=1);

namespace Modularity\Module\Posts\TemplateController;

use Modularity\Helper\WpService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class AbstractControllerTest extends TestCase
{
    protected function setUp(): void
    {
        WpService::set(new FakeWpService([]));
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
        $post = $this->getPostObjectFake([
            'getSchemaProperty' => static fn($property) => $property === '@type' ? 'Event' : null,
        ]);

        $this->assertTrue($controller->postUsesSchemaTypeEvent($post));
    }

    #[TestDox('postUsesSchemaTypeEvent() returns false if schema @type is not Event')]
    public function testPostUsesSchemaTypeEventReturnsFalse()
    {
        $controller = new AbstractController($this->getModuleMock());
        $post = $this->getPostObjectFake([
            'getSchemaProperty' => static fn($property) => $property === '@type' ? 'Article' : null,
        ]);

        $this->assertFalse($controller->postUsesSchemaTypeEvent($post));
    }

    private function getModuleMock(): \Modularity\Module\Posts\Posts|MockObject
    {
        $module = $this->createMock(\Modularity\Module\Posts\Posts::class);
        $module->data = ['posts' => [], 'postsSources' => []];
        $module->fields = ['posts_columns' => 2];
        $module->domainChecker = $this->createMock(\Modularity\Module\Posts\Helper\DomainChecker::class);

        return $module;
    }

    private function getPostObjectFake(array $returns): object
    {
        return new class($returns) {
            public function __construct(
                private array $returns,
            ) {}

            public function getSchemaProperty($property)
            {
                return $this->returns['getSchemaProperty']($property);
            }
        };
    }
}
