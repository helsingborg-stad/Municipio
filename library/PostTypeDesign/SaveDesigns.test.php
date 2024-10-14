<?php

namespace Municipio\PostTypeDesign;

use PHPUnit\Framework\TestCase;
use Municipio\PostTypeDesign\SaveDesigns;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\UpdateOption;
use Municipio\PostTypeDesign\ConfigFromPageIdInterface;
use WpService\Contracts\AddFilter;

/**
 * @group wp_mock
 */
class SaveDesignsTest extends TestCase
{
    public function testActionsAdded()
    {
        $wpService = $this->getWpService();

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig([[], '123']));

        $saveDesignsInstance->addHooks();

        $this->assertEquals('customize_save_after', $wpService->calls['addAction'][0][0]);
    }

    public function testStoreDesignsReturnsIfNoPostTypes()
    {
        $wpService = $this->getWpService(['postTypes' => []]);

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig([[], '123']));

        $saveDesignsInstance->storeDesigns();

        $this->assertCount(0, $wpService->calls['getOption']);
    }

    public function testStoreSkipsIfNoDesign()
    {
        $wpService = $this->getWpService([
            'postTypes'   => ['post'],
            'getThemeMod' => [false, true],
        ]);

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig([[], '123']));

        $saveDesignsInstance->storeDesigns();

        $this->assertEmpty($saveDesignsInstance->designOption);
    }

    public function testStoreDesignRemovesDesignIfEmpty()
    {
        $wpService = $this->getWpService([
            'postTypes'   => ['post'],
            'getThemeMod' => [false, true],
            'getOption'   => ['post' => ['mods']]
        ]);

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig([[], '123']));

        $saveDesignsInstance->storeDesigns();

        $this->assertEmpty($saveDesignsInstance->designOption);
    }

    public function testStoreDesignAlwaysUpdatesOptionIfPostTypesExists()
    {
        $wpService = $this->getWpService([
            'postTypes'   => ['post'],
            'getThemeMod' => ['123', true, ['abc']]
        ]);

        $key                 = 'color_palette_primary';
        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig([[$key => [
            'base' => '#000'
        ]
        ], '123']));

        $saveDesignsInstance->storeDesigns();


        $this->assertArrayHasKey($key, $saveDesignsInstance->designOption['post']['design']);
    }

    private function getConfig($returnValue = null)
    {
        return new class ($returnValue) implements ConfigFromPageIdInterface {
            public function __construct(private $returnValue)
            {
            }
            public function get(string $design): array
            {
                return $this->returnValue ?? [[], ''];
            }
        };
    }

    private function getWpService(array $db = []): AddAction&AddFilter&GetOption&GetThemeMod&GetPostTypes&UpdateOption
    {
        return new class ($db) implements AddAction, AddFilter, GetOption, GetThemeMod, GetPostTypes, UpdateOption {
            public array $calls = ['addFilter' => [], 'getOption' => [], 'updateOption' => []];

            public function __construct(private array $db)
            {
            }

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->calls['addAction'][] = func_get_args();
                return true;
            }

            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function getOption(string $option, mixed $defaultValue = false): mixed
            {
                $this->calls['getOption'][] = "ran";
                return $this->db['getOption'] ?? null;
            }

            public function getThemeMod(string $name, mixed $default = false): mixed
            {
                if (!empty($this->db['getThemeMod']) && is_array($this->db['getThemeMod'])) {
                    $themeMod = array_shift($this->db['getThemeMod']);
                } else {
                    $themeMod = $default;
                }

                return $themeMod;
            }

            public function getPostTypes(
                array|string $args = array(),
                string $output = 'names',
                string $operator = 'and'
            ): array {
                return $this->db['postTypes'] ?? [];
            }

            public function updateOption(string $option, mixed $value, string|bool $autoload = null): bool
            {
                $this->calls['updateOption'][] = "ran";
                return true;
            }
        };
    }
}
