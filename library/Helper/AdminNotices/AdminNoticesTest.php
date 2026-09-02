<?php

namespace Municipio\Helper\AdminNotices;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\DeleteTransient;
use WpService\Contracts\GetCurrentUserId;
use WpService\Contracts\GetTransient;
use WpService\Contracts\SetTransient;
use WpService\Contracts\WpAdminNotice;

class AdminNoticesTest extends TestCase {
    #[TestDox('registers the render callback on construction')]
    public function testRegistersRenderCallbackOnConstruction(): void
    {
        $wpService = static::getWpService();
        new AdminNotices($wpService);

        static::assertCount(1, $wpService->actions);
        static::assertEquals('admin_notices', $wpService->actions[0]['hookName']);
    }

    #[TestDox('persists the notice instead of rendering it immediately')]
    public function testAddNoticePersistsInsteadOfRenderingImmediately(): void
    {
        $wpService = static::getWpService();
        $adminNotices = new AdminNotices($wpService);
        $adminNotices->addNotice('Test message', AdminNoticeType::ERROR, false);

        static::assertCount(0, $wpService->notices);
        static::assertNotEmpty($wpService->transients);
    }

    #[TestDox('renders a persisted notice when the admin_notices hook fires, even on a later request')]
    public function testRendersPersistedNoticeOnAdminNoticesHook(): void
    {
        $wpService = static::getWpService();
        $adminNotices = new AdminNotices($wpService);
        $adminNotices->addNotice('Test message', AdminNoticeType::ERROR, false);

        // Simulate a fresh request: recreate the service (dropping in-memory state) but keep the transient.
        $freshWpService = static::getWpService();
        $freshWpService->transients = $wpService->transients;
        new AdminNotices($freshWpService);

        // Call the admin_notices hook registered by the fresh instance.
        $freshWpService->actions[0]['callback']();

        static::assertCount(1, $freshWpService->notices);
        static::assertEquals('Test message', $freshWpService->notices[0]['message']);
        static::assertEquals('error', $freshWpService->notices[0]['args']['type']);
        static::assertFalse($freshWpService->notices[0]['args']['dismissible']);
    }

    #[TestDox('does not render the same notice twice')]
    public function testDoesNotRenderTheSameNoticeTwice(): void
    {
        $wpService = static::getWpService();
        $adminNotices = new AdminNotices($wpService);
        $adminNotices->addNotice('Test message', AdminNoticeType::ERROR, false);

        $wpService->actions[0]['callback']();
        $wpService->actions[0]['callback']();

        static::assertCount(1, $wpService->notices);
    }

    private static function getWpService(): AddAction&WpAdminNotice&GetTransient&SetTransient&DeleteTransient&GetCurrentUserId {
        return new class implements AddAction, WpAdminNotice, GetTransient, SetTransient, DeleteTransient, GetCurrentUserId {
            public array $actions = [];
            public array $notices = [];
            public array $transients = [];

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->actions[] = compact('hookName', 'callback', 'priority', 'acceptedArgs');
                return true;
            }

            public function wpAdminNotice(string $message, array $args = []): void
            {
                $this->notices[] = compact('message', 'args');
            }

            public function getTransient(string $transient): mixed
            {
                return $this->transients[$transient] ?? false;
            }

            public function setTransient(string $transient, mixed $value, int $expiration = 0): bool
            {
                $this->transients[$transient] = $value;
                return true;
            }

            public function deleteTransient(string $transient): bool
            {
                unset($this->transients[$transient]);
                return true;
            }

            public function getCurrentUserId(): int
            {
                return 1;
            }
        };
    }
}