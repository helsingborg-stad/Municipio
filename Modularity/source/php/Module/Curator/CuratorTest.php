<?php

declare(strict_types=1);

namespace Modularity\Module\Curator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests Curator AJAX request detection.
 */
class CuratorTest extends TestCase
{
    /**
     * Verify only Curator actions are handled as Curator AJAX requests.
     */
    #[DataProvider('ajaxActionProvider')]
    public function testDetectsOnlyCuratorAjaxActions(string $action, bool $expected): void
    {
        $isCuratorAjaxAction = new \ReflectionMethod(Curator::class, 'isCuratorAjaxAction');
        $curator = (new \ReflectionClass(Curator::class))->newInstanceWithoutConstructor();

        $result = $isCuratorAjaxAction->invoke($curator, $action);

        static::assertSame($expected, $result);
    }

    /**
     * Provide Curator and unrelated AJAX actions.
     *
     * @return array<string, array{string, bool}>
     */
    public static function ajaxActionProvider(): array
    {
        return [
            'feed' => ['mod_curator_get_feed', true],
            'load more' => ['mod_curator_load_more', true],
            'search indexing' => ['municipio_search_index_build', false],
        ];
    }
}
