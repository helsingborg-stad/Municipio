<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin\Indexing;

use Municipio\ProgressReporter\UI\AdminProgressActionButton;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests Search Index admin indexing controls.
 */
class SearchIndexingAdminTest extends TestCase
{
    /**
     * Set the Search Index settings page as the current admin page.
     */
    protected function setUp(): void
    {
        $_GET['page'] = 'municipio-search-index-settings';
    }

    /**
     * Remove request state after each test.
     */
    protected function tearDown(): void
    {
        unset($_GET['page']);
    }

    /**
    * Verify the module registers its UI hook.
     */
    public function testRegistersAdminHooks(): void
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $admin = new SearchIndexingAdmin(
            $wpService,
            $this->createStub(SearchIndexConfig::class),
            new AdminProgressActionButton($wpService),
        );

        $admin->addHooks();

        $actions = array_column($wpService->methodCalls['addAction'], 0);
        static::assertSame(['acf/input/admin_head'], $actions);
    }

    /**
     * Verify users without settings access do not receive the indexing metabox.
     */
    public function testDoesNotRegisterMetaBoxForUnauthorizedUser(): void
    {
        $wpService = new FakeWpService(['currentUserCan' => false]);
        $admin = new SearchIndexingAdmin(
            $wpService,
            $this->createStub(SearchIndexConfig::class),
            new AdminProgressActionButton($wpService),
        );

        $admin->registerMetaBox();

        static::assertArrayNotHasKey('addMetaBox', $wpService->methodCalls);
    }

    /**
     * Verify an authorized user receives the indexing metabox.
     */
    public function testRegistersMetaBoxForAdministrator(): void
    {
        $wpService = new FakeWpService([
            'currentUserCan' => true,
            '__' => static fn(string $text): string => $text,
            'addMetaBox' => true,
        ]);
        $admin = new SearchIndexingAdmin(
            $wpService,
            $this->createStub(SearchIndexConfig::class),
            new AdminProgressActionButton($wpService),
        );

        $admin->registerMetaBox();

        static::assertSame('municipio-search-index-indexing', $wpService->methodCalls['addMetaBox'][0][0]);
        static::assertSame('side', $wpService->methodCalls['addMetaBox'][0][4]);
    }

    /**
     * Verify indexing is disabled until a provider is configured.
     */
    public function testDisablesButtonForUnconfiguredProvider(): void
    {
        $config = $this->createMock(SearchIndexConfig::class);
        $config->method('isConfigured')->willReturn(false);
        $wpService = new FakeWpService([
            'adminUrl' => 'https://example.test/wp-admin/admin-ajax.php',
            'wpCreateNonce' => 'nonce-value',
            'escUrl' => static fn(string $value): string => $value,
            'escAttr' => static fn(string $value): string => $value,
            'escHtml' => static fn(string $value): string => $value,
            '__' => static fn(string $text): string => $text,
        ]);
        $admin = new SearchIndexingAdmin($wpService, $config, new AdminProgressActionButton($wpService));

        ob_start();
        $admin->render();
        $output = (string) ob_get_clean();

        static::assertStringContainsString(
            'data-js-progress-url="https://example.test/wp-admin/admin-ajax.php?action=municipio_search_index_build"',
            $output,
        );
        static::assertStringContainsString('data-js-progress-method="post"', $output);
        static::assertStringContainsString('data-js-progress-nonce="nonce-value"', $output);
        static::assertStringContainsString(' disabled', $output);
        static::assertStringContainsString('Configure and save a search provider before indexing.', $output);
    }
}