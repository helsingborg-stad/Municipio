<?php

declare(strict_types=1);

namespace Municipio\ProgressReporter\UI;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests shared admin progress action button markup.
 */
class AdminProgressActionButtonTest extends TestCase
{
    /**
     * Verify GET buttons contain parameters and a URL nonce.
     */
    public function testRendersGetButton(): void
    {
        $wpService = $this->createWpService([
            'wpNonceUrl' => static fn(string $url, string $action): string => $url . '&_wpnonce=' . $action,
        ]);

        $markup = (new AdminProgressActionButton($wpService))->renderGet(new AdminProgressActionButtonConfig(
            action: 'example_sync',
            label: 'Sync content',
            parameters: ['post_type' => 'place'],
        ));

        static::assertStringContainsString('type="button"', $markup);
        static::assertStringContainsString('class="button button-primary"', $markup);
        static::assertStringContainsString(
            'data-js-progress-url="https://example.test/wp-admin/admin-ajax.php?action=example_sync&amp;post_type=place&amp;_wpnonce=example_sync"',
            $markup,
        );
        static::assertStringNotContainsString('data-js-progress-method', $markup);
        static::assertStringContainsString('>Sync content</button>', $markup);
    }

    /**
     * Verify POST buttons contain shared transport attributes and disabled state.
     */
    public function testRendersPostButton(): void
    {
        $markup = (new AdminProgressActionButton($this->createWpService()))->renderPost(
            new AdminProgressActionButtonConfig(
                action: 'example_build',
                label: 'Build index',
                state: AdminProgressActionButtonState::Disabled,
                errorMessage: 'Request failed',
            ),
        );

        static::assertStringContainsString(
            'data-js-progress-url="https://example.test/wp-admin/admin-ajax.php?action=example_build"',
            $markup,
        );
        static::assertStringContainsString('data-js-progress-method="post"', $markup);
        static::assertStringContainsString('data-js-progress-nonce="nonce-value"', $markup);
        static::assertStringContainsString('data-js-progress-error-message="Request failed"', $markup);
        static::assertStringContainsString(' disabled', $markup);
    }

    /**
     * Create a fake WordPress service with escaping enabled.
     *
     * @param array<string, mixed> $methods
     */
    private function createWpService(array $methods = []): FakeWpService
    {
        return new FakeWpService([
            'adminUrl' => 'https://example.test/wp-admin/admin-ajax.php',
            'wpCreateNonce' => 'nonce-value',
            'wpNonceUrl' => static fn(string $url): string => $url,
            'escUrl' => static fn(string $value): string => str_replace('&', '&amp;', $value),
            'escAttr' => static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES),
            'escHtml' => static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES),
            ...$methods,
        ]);
    }
}