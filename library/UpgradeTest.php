<?php

namespace Municipio;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class UpgradeTest extends TestCase
{
    #[TestDox('v_33 migrates post type schema settings')]
    public function testV33()
    {
        $wpService  = new FakeWpService(['getPostTypes' => ['test_post_type'], 'addAction' => true]);
        $acfService = new FakeAcfService(['getField' => 'Thing', 'updateField' => true]);
        $upgrade    = new Upgrade($wpService, $acfService);

        $upgrade->v_33((object)[]);

        $this->assertEquals('schema', $acfService->methodCalls['getField'][0][0]);
        $this->assertEquals('test_post_type_options', $acfService->methodCalls['getField'][0][1]);
        $this->assertEquals('post_type_schema_types', $acfService->methodCalls['updateField'][0][0]);
        $this->assertEquals([['post_type' => 'test_post_type', 'schema_type' => 'Thing']], $acfService->methodCalls['updateField'][0][1]);
    }

    #[TestDox('v_44 migrates legacy SearchIndex settings')]
    public function testV44(): void
    {
        $values = [
            'algolia_index_application_id' => 'legacy-application-id',
            'algolia_index_api_key' => implode('-', ['legacy', 'api', 'key']),
        ];
        $acfService = new FakeAcfService([
            'getField' => static function (string $field) use (&$values): mixed {
                return $values[$field] ?? false;
            },
            'updateField' => static function (string $field, mixed $value) use (&$values): bool {
                $values[$field] = $value;
                return true;
            },
        ]);
        $wpService = new FakeWpService([
            'addAction' => true,
            'getOption' => static fn(string $option, mixed $default): mixed => $default,
            'updateOption' => true,
        ]);
        $upgrade = new Upgrade($wpService, $acfService);

        static::assertTrue($upgrade->v_44());
        static::assertSame('legacy-application-id', $values['search_index_algolia_application_id']);
        static::assertSame('algolia', $values['search_index_provider']);
        static::assertSame('municipio_search_index_legacy_settings_migrated', $wpService->methodCalls['updateOption'][0][0]);
    }
}
