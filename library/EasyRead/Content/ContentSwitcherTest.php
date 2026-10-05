<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Content;

use Municipio\EasyRead\Config\EasyReadConfigInterface;
use Municipio\EasyRead\Request\ReadableRequestInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ContentSwitcherTest extends TestCase
{
    #[TestDox('runs the alternative-content filter before WordPress renders Gutenberg blocks')]
    public function testRegistersContentReplacementBeforeBlockRendering(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);
        $switcher = $this->createSwitcherWithWpService($wpService, '<p>Alternative</p>');

        $switcher->addHooks();

        $contentFilters = array_values(array_filter(
            $wpService->methodCalls['addFilter'],
            static fn(array $arguments): bool => $arguments[0] === 'the_content',
        ));

        static::assertCount(1, $contentFilters);
        static::assertSame(8, $contentFilters[0][2]);
    }

    #[TestDox('passes Gutenberg alternative content to WordPress block rendering unchanged')]
    public function testReplaceContentPreservesGutenbergBlocks(): void
    {
        $alternative = '<!-- wp:paragraph --><p>Easy read</p><!-- /wp:paragraph -->';

        $switcher = $this->createSwitcher($alternative, ['hasBlocks' => true]);

        static::assertSame($alternative, $switcher->replaceContent('<p>Original</p>'));
    }

    #[TestDox('keeps the legacy More-tag lead behavior for classic-editor alternative content')]
    public function testReplaceContentFormatsClassicEditorLead(): void
    {
        $switcher = $this->createSwitcher('A simple lead<!--more--><p>More content</p>', ['hasBlocks' => false]);

        static::assertSame('<p class="lead">A simple lead</p><p>More content</p>', $switcher->replaceContent('<p>Original</p>'));
    }

    private function createSwitcher(string $alternative, array $wpSettings): ContentSwitcher
    {
        $wpService = new FakeWpService([...[
            'isPostTypeArchive' => false,
            'getQueriedObjectId' => 123,
            'getTheID' => 123,
        ], ...$wpSettings]);

        return $this->createSwitcherWithWpService($wpService, $alternative);
    }

    private function createSwitcherWithWpService(FakeWpService $wpService, string $alternative): ContentSwitcher
    {

        $repository = new class ($alternative) implements AlternativeContentRepositoryInterface {
            public function __construct(private string $alternative) {}

            public function hasAlternative(int $postId): bool
            {
                return $postId === 123;
            }

            public function getAlternative(int $postId): string
            {
                return $this->alternative;
            }
        };

        $request = $this->createMock(ReadableRequestInterface::class);
        $request->method('isReadable')->willReturn(true);

        return new ContentSwitcher(
            $wpService,
            $repository,
            $this->createMock(EasyReadConfigInterface::class),
            $request,
            $this->createMock(CurrentUrlInterface::class),
        );
    }
}
