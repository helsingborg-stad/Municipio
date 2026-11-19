<?php

declare(strict_types=1);

namespace Municipio\Chat\Admin;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WpService\Implementations\FakeWpService;

/**
 * The constructor type-hints ComponentLibrary\Renderer\RendererInterface, which is not
 * available in the test environment. The instance is therefore created without its
 * constructor and only the WP service is injected; render() is not covered here.
 */
class ChatStatsMetaBoxTest extends TestCase
{
    private array $originalGet;

    protected function setUp(): void
    {
        $this->originalGet = $_GET;
    }

    protected function tearDown(): void
    {
        $_GET = $this->originalGet;
    }

    #[TestDox('addHooks() registers the meta box on admin_menu')]
    public function testAddHooksRegistersOnAdminMenu(): void
    {
        $wpService = $this->getWpService();
        $metaBox = $this->createMetaBox($wpService);

        $metaBox->addHooks();

        static::assertSame('admin_menu', $wpService->methodCalls['addAction'][0][0]);
        static::assertSame([$metaBox, 'register'], $wpService->methodCalls['addAction'][0][1]);
    }

    #[TestDox('register() adds the statistics meta box on the chat-settings page')]
    public function testRegisterAddsMetaBoxOnChatSettingsPage(): void
    {
        $_GET['page'] = 'chat-settings';
        $wpService = $this->getWpService();
        $metaBox = $this->createMetaBox($wpService);

        $metaBox->register();

        static::assertCount(1, $wpService->methodCalls['addMetaBox']);
        [$id, $title, $callback, $screen, $context] = $wpService->methodCalls['addMetaBox'][0];
        static::assertSame('chat-statistics', $id);
        static::assertSame('Chat Statistics', $title);
        static::assertSame([$metaBox, 'render'], $callback);
        static::assertSame('acf_options_page', $screen);
        static::assertSame('side', $context);
    }

    #[TestDox('register() does nothing on other admin pages')]
    public function testRegisterSkipsOtherPages(): void
    {
        $_GET['page'] = 'some-other-page';
        $wpService = $this->getWpService();

        $this->createMetaBox($wpService)->register();

        static::assertArrayNotHasKey('addMetaBox', $wpService->methodCalls);
    }

    #[TestDox('register() does nothing when no page query parameter is set')]
    public function testRegisterSkipsWhenNoPageParameter(): void
    {
        unset($_GET['page']);
        $wpService = $this->getWpService();

        $this->createMetaBox($wpService)->register();

        static::assertArrayNotHasKey('addMetaBox', $wpService->methodCalls);
    }

    private function createMetaBox(FakeWpService $wpService): ChatStatsMetaBox
    {
        $reflection = new ReflectionClass(ChatStatsMetaBox::class);
        $metaBox = $reflection->newInstanceWithoutConstructor();
        $reflection->getProperty('wpService')->setValue($metaBox, $wpService);

        return $metaBox;
    }

    private function getWpService(): FakeWpService
    {
        return new FakeWpService([
            '__' => static fn(string $text) => $text,
            'addAction' => true,
        ]);
    }
}
