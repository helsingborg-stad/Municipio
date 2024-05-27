<?php

namespace Municipio\PostTypeDesign;

use PHPUnit\Framework\TestCase;
use Municipio\PostTypeDesign\SaveDesigns;
/* SaveDesigns */
use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\UpdateOption;
/* ConfigFromPageId */
use Municipio\PostTypeDesign\ConfigFromPageId;
use WpService\Contracts\IsWPError;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemoteRetrieveBody;
use WP_Error;

class SaveDesignsTest extends TestCase
{
    public function testActionsAdded()
    {
        $wpService = $this->getWpService();

        $saveDesignsInstance = new SaveDesigns('name', $wpService, new ConfigFromPageId($wpService));

        $saveDesignsInstance->addHooks();

        $this->assertEquals('customize_save_after', $wpService->calls['addAction'][0][0]);
    }

    public function testStoreDesignsReturnsIfNoPostTypes()
    {
        $wpService = $this->getWpService(['postTypes' => ['post']]);

        $saveDesignsInstance = new SaveDesigns('name', $wpService, new ConfigFromPageId($wpService));

        $saveDesignsInstance->storeDesigns();

        $this->assertCount(0, $wpService->getOptionRanTimes);
    }


    private function getWpService(array $db = []): AddAction
    {
        return new class ($db) implements AddAction, GetOption, GetThemeMod, GetPostTypes, UpdateOption, IsWPError, WpRemoteGet, WpRemoteRetrieveBody {
            public array $calls             = ['addFilter' => []];
            public array $getOptionRanTimes = [];

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
                $this->getOptionRanTimes[] = "ran";
                return $this->db['option'] = null;
            }

            public function getThemeMod(string $name, mixed $default = false): mixed
            {
                return $default;
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
                return true;
            }

            public function isWPError(mixed $thing): bool
            {
                return false;
            }

            public function wpRemoteGet(string $url, array $args = []): array|WP_Error
            {
                return [];
            }

            public function wpRemoteRetrieveBody(array|WP_Error $response): string
            {
                return "";
            }
        };
    }
}
