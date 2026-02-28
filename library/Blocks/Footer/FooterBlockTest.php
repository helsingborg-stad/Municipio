<?php

namespace Municipio\Blocks\Footer;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Block_Type;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\RegisterBlockType;

class FooterBlockTest extends TestCase
{
    #[TestDox('registers block type on init')]
    public function testRegisterBlockType(): void
    {
        $wpService = static::createWpService();
        $footerBlock = new FooterBlock($wpService);

        $footerBlock->addHooks();
        $callback = $wpService->addedActions[0][1];
        $callback();

        $this->assertCount(1, $wpService->registeredBlockTypes);

        // Assert that the registered block path contains a block.json file
        $registeredBlockType = $wpService->registeredBlockTypes[0][0];
        static::assertFileExists($registeredBlockType . '/block.json');
    }

    #[TestDox('adds view path for footer block')]
    public function testAddViewPathFilter(): void
    {
        $wpService = static::createWpService();
        $footerBlock = new FooterBlock($wpService);

        $footerBlock->addHooks();
        $callback = $wpService->addedFilters[0][1];
        $paths = ['existing/path'];
        $result = $callback($paths);

        static::assertCount(2, $result);
        static::assertContains('existing/path', $result);
        static::assertContains(__DIR__ . '/views', $result);
    }

    private static function createWpService(): RegisterBlockType&AddAction&AddFilter
    {
        return new class implements RegisterBlockType, AddAction, AddFilter {
            public array $addedActions = [];
            public array $addedFilters = [];
            public array $registeredBlockTypes = [];

            public function registerBlockType(string|WP_Block_Type $blockType, array $args = []): WP_Block_Type|false
            {
                $this->registeredBlockTypes[] = [$blockType, $args];
                return false;
            }

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addedActions[] = [$hookName, $callback, $priority, $acceptedArgs];
                return true;
            }

            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addedFilters[] = [$hookName, $callback, $priority, $acceptedArgs];
                return true;
            }
        };
    }
}
