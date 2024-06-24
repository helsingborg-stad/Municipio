<?php

namespace Municipio;

use AcfService\Contracts\UpdateField;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\GetThemeMod;

class UpgradeTest extends TestCase
{
    public function testV30MigratesContentTypesToSchemaTypeIfContentTypeFoundInPostType()
    {
        $wpService  = $this->getWpService([
            'getPostTypes' => ['test_post_type'],
            'getThemeMod'  => ['municipio_customizer_panel_content_types_test_post_type_content_type' => 'place']
        ]);
        $acfService = $this->getAcfService();
        $upgrade    = new Upgrade($wpService, $acfService);

        $upgrade->v_30(null);
        $updateFieldCalls = $acfService->calls['updateField'];

        $this->assertEquals('schema', $updateFieldCalls[1][0]);
        $this->assertEquals('Place', $updateFieldCalls[1][1]);
        $this->assertEquals('test_post_type_options', $updateFieldCalls[1][2]);
    }

    public function testV30SetsFeatureEnabledIfHasPreviousContentTypesSet()
    {
        $wpService  = $this->getWpService([
            'getPostTypes' => ['test_post_type'],
            'getThemeMod'  => ['municipio_customizer_panel_content_types_test_post_type_content_type' => 'place']
        ]);
        $acfService = $this->getAcfService();
        $upgrade    = new Upgrade($wpService, $acfService);

        $upgrade->v_30(null);

        $this->assertEquals('mun_schemadata_enabled', $acfService->calls['updateField'][0][0]);
        $this->assertEquals(true, $acfService->calls['updateField'][0][1]);
        $this->assertEquals('options', $acfService->calls['updateField'][0][2]);
    }

    private function getWpService(array $data = []): GetThemeMod&GetPostTypes
    {
        return new class ($data) implements GetThemeMod, GetPostTypes {
            public array $calls = [
                'getThemeMod'  => [],
                'getPostTypes' => []
            ];

            public function __construct(private array $data)
            {
            }

            public function getThemeMod(string $name, mixed $defaultValue = false): mixed
            {
                $this->calls['getThemeMod'][] = func_get_args();
                return $this->data['getThemeMod'][$name] ?? $defaultValue;
            }

            public function getPostTypes(
                array|string $args = array(),
                string $output = 'names',
                string $operator = 'and'
            ): array {
                $this->calls['getPostTypes'][] = func_get_args();
                return $this->data['getPostTypes'] ?? [];
            }
        };
    }

    public function getAcfService(): UpdateField
    {
        return new class implements UpdateField {
            public array $calls = [];

            public function updateField(string $selector, mixed $value, mixed $postId = false): bool
            {
                $this->calls['updateField'][] = func_get_args();
                return true;
            }
        };
    }
}
