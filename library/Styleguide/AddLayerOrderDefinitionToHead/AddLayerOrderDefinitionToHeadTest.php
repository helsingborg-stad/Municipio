<?php

declare(strict_types=1);


namespace Municipio\Styleguide\AddLayerOrderDefinitionToHead;

use Municipio\Test\GetThemeFilters;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;

class AddLayerOrderDefinitionToHeadTest extends TestCase
{
    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $sut = new AddLayerOrderDefinitionToHead(static::createWpService());
        static::assertInstanceOf(AddLayerOrderDefinitionToHead::class, $sut);
    }

    #[TestDox('applies filter to MarkupProcessor')]
    public function testAppliesFilterToMarkupProcessor(): void
    {
        $wpService = static::createWpService();
        $sut = new AddLayerOrderDefinitionToHead($wpService);

        $sut->addHooks();

        foreach ($wpService->addedFilters as $filter) {
            if ($filter['hookName'] !== 'Municipio\MarkupProcessor') { continue; }

static::assertSame([$sut, 'process'], $filter['callback']);
                return;
        }

        static::fail('Filter "Municipio\MarkupProcessor" not found');
    }

    #[TestDox('registers early admin and login hooks')]
    public function testRegistersEarlyAdminAndLoginHooks(): void
    {
        $wpService = static::createWpService();
        $sut = new AddLayerOrderDefinitionToHead($wpService);

        $sut->addHooks();

        static::assertSame(
            [
                [
                    'hookName' => 'admin_print_styles',
                    'callback' => [$sut, 'render'],
                    'priority' => 0,
                    'acceptedArgs' => 1,
                ],
                [
                    'hookName' => 'login_head',
                    'callback' => [$sut, 'render'],
                    'priority' => 0,
                    'acceptedArgs' => 1,
                ],
            ],
            $wpService->addedActions,
        );
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

    #[TestDox('renders the style tag for admin and login heads')]
    public function testRenderOutputsStyleTag(): void
    {
        $wpService = static::createWpService();
        $sut = new AddLayerOrderDefinitionToHead($wpService);

        ob_start();
        $sut->render();
        $output = ob_get_clean();

        static::assertSame(
            '<style>@layer wordpress, generic, elements, objects, components, icons, utilities, theme;</style>',
            $output,
        );
    }

    private static function createWpService(): AddAction&AddFilter
    {
        return new class implements AddAction, AddFilter {
            public array $addedActions = [];
            public array $addedFilters = [];

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addedActions[] = [
                    'hookName' => $hookName,
                    'callback' => $callback,
                    'priority' => $priority,
                    'acceptedArgs' => $acceptedArgs,
                ];

                return true;
            }

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
