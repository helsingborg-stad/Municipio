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

class SaveDesignsTest extends TestCase
{
    public function testActionsAdded()
    {
        $wpService = $this->getWpService();

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig(['mods' => [], '123']));

        $saveDesignsInstance->addHooks();

        $this->assertEquals('customize_save_after', $wpService->calls['addAction'][0][0]);
    }

    public function testStoreDesignsReturnsIfNoPostTypes()
    {
        $wpService = $this->getWpService(['postTypes' => []]);

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig(['mods' => [], '123']));

        $saveDesignsInstance->storeDesigns();

        $this->assertCount(0, $wpService->calls['getOption']);
    }

    public function testStoreSkipsIfNoDesign()
    {
        $wpService = $this->getWpService([
            'postTypes'   => ['post'],
            'getThemeMod' => [false, true],
        ]);

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig(['mods' => [], '123']));

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

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig(['mods' => [], '123']));

        $saveDesignsInstance->storeDesigns();

        $this->assertEmpty($saveDesignsInstance->designOption);
    }

    public function testStoreDesignCreatesInlineCss()
    {
        $wpService = $this->getWpService([
            'postTypes'   => ['post'],
            'getThemeMod' => ['123', true],
            'getOption'   => [
                'post'  => [
                    'mods'      => ['key' => 'value'],
                    'designId'  => '123',
                    'inlineCss' => 'post: css;'
                ],
                'post2' => [
                    'mods'      => ['key' => 'value'],
                    'designId'  => '321',
                    'inlineCss' => 'post2: css;'
                ]
            ]
        ]);

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig(['mods' => [], '123']));

        $saveDesignsInstance->storeDesigns();
        $this->assertEquals('post: css;post2: css;', $saveDesignsInstance->designOption['inlineCss']);
    }


    public function testStoreDesignAlwaysUpdatesOptionIfPostTypesExists()
    {
        $wpService = $this->getWpService([
            'postTypes' => ['post']
        ]);

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig(['mods' => [], '123']));

        $saveDesignsInstance->storeDesigns();

        $this->assertCount(1, $wpService->calls['updateOption']);
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

    private function getWpService(array $db = []): AddAction
    {
        return new class ($db) implements AddAction, GetOption, GetThemeMod, GetPostTypes, UpdateOption {
            public array $calls = ['addFilter' => [], 'getOption' => [], 'updateOption' => []];

            public function __construct(private array $db)
            {
            }

            public function addAction(string $tag, callable $functionToAdd, int $priority = 10, int $acceptedArgs = 1): bool
            {
                $this->calls['addAction'][] = func_get_args();
                return true;
            }

            public function getOption(string $option, mixed $defaultValue = false): mixed
            {
                $this->calls['getOption'][] = "ran";
                return $this->db['getOption'] ?? null;
            }

            public function getThemeMod(string $name, mixed $default = false): mixed
            {
                $themeMod = false;

                if (!empty($this->db['getThemeMod'][0])) {
                    $themeMod = $this->db['getThemeMod'][0];
                    unset($this->db['getThemeMod'][0]);
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
