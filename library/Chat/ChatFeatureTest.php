<?php

namespace Municipio\Chat;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class ChatFeatureTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $feature = new ChatFeature(
            $this->getWpService(),
            $this->getEnqueueManager(),
            $this->getAcfService()
        );

        $this->assertInstanceOf(ChatFeature::class, $feature);
    }

    #[TestDox('isEnabled() returns true when the chat_enabled option is true')]
    public function testIsEnabledReturnsTrueWhenAcfFieldIsTrue(): void
    {
        $feature = new ChatFeature(
            $this->getWpService(),
            $this->getEnqueueManager(),
            $this->getAcfService(['chat_enabled' => true])
        );

        $this->assertTrue($feature->isEnabled());
    }

    #[TestDox('isEnabled() returns false when the chat_enabled option is false')]
    public function testIsEnabledReturnsFalseWhenAcfFieldIsFalse(): void
    {
        $feature = new ChatFeature(
            $this->getWpService(),
            $this->getEnqueueManager(),
            $this->getAcfService(['chat_enabled' => false])
        );

        $this->assertFalse($feature->isEnabled());
    }

    #[TestDox('isEnabled() returns false when the chat_enabled option is unset')]
    public function testIsEnabledReturnsFalseWhenAcfFieldIsUnset(): void
    {
        $feature = new ChatFeature(
            $this->getWpService(),
            $this->getEnqueueManager(),
            $this->getAcfService()
        );

        $this->assertFalse($feature->isEnabled());
    }

    #[TestDox('isGlobalChatEnabled() returns true when the chat_global_enabled option is true')]
    public function testIsGlobalChatEnabledReturnsTrueWhenAcfFieldIsTrue(): void
    {
        $feature = new ChatFeature(
            $this->getWpService(),
            $this->getEnqueueManager(),
            $this->getAcfService(['chat_global_enabled' => true])
        );

        $this->assertTrue($feature->isGlobalChatEnabled());
    }

    #[TestDox('isGlobalChatEnabled() returns false when the chat_global_enabled option is false')]
    public function testIsGlobalChatEnabledReturnsFalseWhenAcfFieldIsFalse(): void
    {
        $feature = new ChatFeature(
            $this->getWpService(),
            $this->getEnqueueManager(),
            $this->getAcfService(['chat_global_enabled' => false])
        );

        $this->assertFalse($feature->isGlobalChatEnabled());
    }

    #[TestDox('addAdminPage() registers an ACF options page')]
    public function testAddAdminPageCallsAcfServiceAddOptionsPage(): void
    {
        $acfService = $this->getAcfService();

        $feature = new ChatFeature(
            $this->getWpService(),
            $this->getEnqueueManager(),
            $acfService
        );

        $feature->addAdminPage();

        $this->assertArrayHasKey('addOptionsPage', $acfService->methodCalls);
        $this->assertCount(1, $acfService->methodCalls['addOptionsPage']);
    }

    #[TestDox('enable() registers the init action and returns early when the feature is disabled')]
    public function testEnableAddsInitActionAndReturnsEarlyWhenDisabled(): void
    {
        $wpService = $this->getWpService();

        $feature = new ChatFeature(
            $wpService,
            $this->getEnqueueManager(),
            $this->getAcfService(['chat_enabled' => false])
        );

        $feature->enable();

        $this->assertCount(1, $wpService->methodCalls['addAction'] ?? []);
        $this->assertSame('init', $wpService->methodCalls['addAction'][0][0]);
    }

    private function getWpService(): FakeWpService
    {
        return new FakeWpService([
            'addAction'    => true,
            'addFilter'    => true,
            'applyFilters' => fn($tag, $value) => $value,
            'wpCacheGet'   => false,
            'wpCacheSet'   => true,
            'getOption'    => false,
            '__'           => '',
        ]);
    }

    private function getAcfService(array $fields = []): FakeAcfService
    {
        return new FakeAcfService([
            'getField' => function (string $selector) use ($fields) {
                return $fields[$selector] ?? null;
            },
        ]);
    }

    private function getEnqueueManager(): EnqueueManagerInterface
    {
        return $this->createMock(EnqueueManagerInterface::class);
    }
}
