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

        $this->assertCount(0, $wpService->getOptionCalls);
    }

    public function testStoreDesignsSkipsIfDesignExists()
    {
        $wpService = $this->getWpService([
            'postTypes' => ['post'],
            'getOption' => ['post_type_design' => ['post' => '123']]
        ]);

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig(['mods' => [], '123']));

        $saveDesignsInstance->storeDesigns();

        $this->assertCount(2, $wpService->getThemeModCalls);
        $this->assertCount(1, $wpService->getOptionCalls);
        $this->assertCount(0, $wpService->getUpdateOptionCalls);
    }

    public function testStoreDesignsUpdatesDesignIfCssOrMods()
    {
        $wpService = $this->getWpService([
            'postTypes'   => ['post'],
            'getOption'   => ['post_type_design' => ['post' => '123']],
            'getThemeMod' => '123'
        ]);

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig([['abc' => ''], null]));

        $saveDesignsInstance->storeDesigns();

        $this->assertCount(1, $wpService->getUpdateOptionCalls);
    }


    public function testStoreDesignsDoesNothingIfNoCssOrMods()
    {
        $wpService = $this->getWpService([
            'postTypes'   => ['post'],
            'getOption'   => ['post_type_design' => ['post' => '123']],
            'getThemeMod' => '123'
        ]);

        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig());

        $saveDesignsInstance->storeDesigns();

        $this->assertCount(0, $wpService->getUpdateOptionCalls);
    }

    public function testTryUpdateOptionWithDesignUpdatesIfDesign()
    {
        $wpService           = $this->getWpService();
        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig([['mod' => 'mod'], '123']));

        $saveDesignsInstance->tryUpdateOptionWithDesign('123', ['post_type_design' => ['post' => '123']], 'post');

        $this->assertCount(1, $wpService->getUpdateOptionCalls);
    }

    public function testTryUpdateOptionWithDesignDoesNotUpdateIfNoDesign()
    {
        $wpService           = $this->getWpService();
        $saveDesignsInstance = new SaveDesigns('name', $wpService, $this->getConfig([[], '']));

        $saveDesignsInstance->tryUpdateOptionWithDesign('123', ['post_type_design' => ['post' => '123']], 'post');

        $this->assertCount(0, $wpService->getUpdateOptionCalls);
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
            public array $calls                = ['addFilter' => []];
            public array $getOptionCalls       = [];
            public array $getThemeModCalls     = [];
            public array $getUpdateOptionCalls = [];

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
                $this->getOptionCalls[] = "ran";
                return $this->db['getOption'] ?? null;
            }

            public function getThemeMod(string $name, mixed $default = false): mixed
            {
                $this->getThemeModCalls[] = "ran";
                return $this->db['getThemeMod'] ?? $default;
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
                $this->getUpdateOptionCalls[] = "ran";
                return true;
            }
        };
    }
}
