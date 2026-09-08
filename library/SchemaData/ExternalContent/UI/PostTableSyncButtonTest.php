<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ExternalContent\UI;

use Municipio\ProgressReporter\UI\AdminProgressActionButton;
use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
use Municipio\SchemaData\ExternalContent\Rest\AjaxSync;
use PHPUnit\Framework\TestCase;
use WP_Screen;
use WpService\Implementations\FakeWpService;

/**
 * Tests the ExternalContent post-table progress action.
 */
class PostTableSyncButtonTest extends TestCase
{
    /**
     * Verify matching post types render the shared GET progress button.
     */
    public function testRendersSharedProgressButton(): void
    {
        $screen = new WP_Screen('edit-place');
        $screen->post_type = 'place';
        $sourceConfig = $this->createStub(SourceConfigInterface::class);
        $sourceConfig->method('getPostType')->willReturn('place');
        $wpService = new FakeWpService([
            'getCurrentScreen' => $screen,
            'adminUrl' => 'https://example.test/wp-admin/admin-ajax.php',
            'wpNonceUrl' => static fn(string $url): string => $url . '&_wpnonce=nonce-value',
            'wpCreateNonce' => 'nonce-value',
            'escUrl' => static fn(string $value): string => str_replace('&', '&amp;', $value),
            'escAttr' => static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES),
            'escHtml' => static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES),
            '__' => static fn(string $text): string => $text,
        ]);
        $button = new PostTableSyncButton(
            [$sourceConfig],
            $wpService,
            new AdminProgressActionButton($wpService),
        );

        ob_start();
        $button->addSyncButton('top');
        $markup = (string) ob_get_clean();

        static::assertStringContainsString('type="button"', $markup);
        static::assertStringContainsString(
            'data-js-progress-url="https://example.test/wp-admin/admin-ajax.php?action=' . AjaxSync::$action
                . '&amp;post_type=place&amp;_wpnonce=nonce-value"',
            $markup,
        );
        static::assertStringContainsString('>Sync all posts from remote source</button>', $markup);
    }
}