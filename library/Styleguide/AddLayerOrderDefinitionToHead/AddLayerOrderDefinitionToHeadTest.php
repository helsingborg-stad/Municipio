<?php

namespace Municipio\Styleguide\AddLayerOrderDefinitionToHead;

use Municipio\Test\GetThemeFilters;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class AddLayerOrderDefinitionToHeadTest extends TestCase
{
    use GetThemeFilters;

    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $sut = new AddLayerOrderDefinitionToHead(static::createWpService());
        $this->assertInstanceOf(AddLayerOrderDefinitionToHead::class, $sut);
    }

    #[TestDox('applies filter to MarkupProcessor')]
    public function testAppliesFilterToMarkupProcessor(): void
    {
        $wpService = static::createWpService();
        $sut = new AddLayerOrderDefinitionToHead($wpService);

        $sut->addHooks();

        foreach ($wpService->addedFilters as $filter) {
            if ($filter['hookName'] === 'Municipio\MarkupProcessor') {
                static::assertSame([$sut, 'process'], $filter['callback']);
                return;
            }
        }

        static::fail('Filter "Municipio\MarkupProcessor" not found');
    }

    #[TestDox('target filter is present in theme')]
    public function testTargetFilterIsPresentInTheme(): void
    {
        $filters = static::getThemeFilters();
        static::assertContains('Municipio\MarkupProcessor', $filters, 'Filter "Municipio\MarkupProcessor" not found in theme');
    }

    #[TestDox('adds style tag as first child of <head>')]
    public function testPrependsStyleTagToHead(): void
    {
        $wpService = static::createWpService();
        $sut = new AddLayerOrderDefinitionToHead($wpService);

        $html = $sut->process('<head><title>Test</title></head>');

        $expectedStyleTag = '<style>@layer wordpress, generic, elements, objects, components, icons, utilities, theme;</style>';
        $expectedHtml = '<head>' . $expectedStyleTag . '<title>Test</title></head>';
        static::assertStringContainsString($expectedHtml, $html);
    }

    private static function createWpService(): AddFilter
    {
        return new class implements AddFilter {
            public array $addedFilters = [];

            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addedFilters[] = [
                    'hookName' => $hookName,
                    'callback' => $callback,
                    'priority' => $priority,
                    'acceptedArgs' => $acceptedArgs,
                ];
                return true;
            }
        };
    }
}
