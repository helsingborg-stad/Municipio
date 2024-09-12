<?php

namespace Municipio\ExternalContent\Config;

use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class SourceConfigFactoryTest extends TestCase
{
    /**
     * @testdox Class exists
     */
    public function testClassExists()
    {
        $this->assertTrue(class_exists('Municipio\ExternalContent\Config\SourceConfigFactory'));
    }

    /**
     * @testdox create returns an array
     */
    public function testCreateReturnsAnArray()
    {
        $factory = new SourceConfigFactory($this->getSchemaDataConfig(), new FakeWpService());
        $this->assertIsArray($factory->create());
    }

    /**
     * @testdox create returns an empty array if no rows are found
     */
    public function testCreateReturnsAnEmptyArrayIfNoRowsAreFound()
    {
        $getOption = fn($option, $default) => $option === 'external_content_sources' ? '0' : $default;
        $wpService = new FakeWpService(['getOption' => $getOption]);
        $factory   = new SourceConfigFactory($this->getSchemaDataConfig(), $wpService);

        $this->assertEmpty($factory->create());
    }

    /**
     * @testdox expected options are fetched
     */
    public function testExpectedOptionsAreFetched()
    {
        $getOption  = fn($option, $default) => $option === 'options_external_content_sources' ? '1' : $default;
        $getOptions = fn($options) => ['options_external_content_sources_0_taxonomies' => '1'];
        $wpService  = new FakeWpService(['getOption' => $getOption, 'getOptions' => $getOptions]);

        (new SourceConfigFactory($this->getSchemaDataConfig(), $wpService))->create();

        $this->assertEquals([
            'options_external_content_sources_0_post_type',
            'options_external_content_sources_0_automatic_import_schedule',
            'options_external_content_sources_0_taxonomies',
            'options_external_content_sources_0_source_type',
            'options_external_content_sources_0_source_json_file_path',
            'options_external_content_sources_0_source_typesense_api_key',
            'options_external_content_sources_0_source_typesense_protocol',
            'options_external_content_sources_0_source_typesense_host',
            'options_external_content_sources_0_source_typesense_port',
            'options_external_content_sources_0_source_typesense_collection',

        ], $wpService->methodCalls['getOptions'][0][0]);

        $this->assertEquals([
            'options_external_content_sources_0_taxonomies_0_from_schema_property',
            'options_external_content_sources_0_taxonomies_0_singular_name',
            'options_external_content_sources_0_taxonomies_0_name',

        ], $wpService->methodCalls['getOptions'][1][0]);
    }

    /**
     * @testdox array of SourceConfigInterface objects are returned
     */
    public function test()
    {
        $getOption  = fn($option, $default) => $option === 'options_external_content_sources' ? '1' : $default;
        $getOptions = fn($options) => [
            'options_external_content_sources_0_post_type'                         => 'test_post_type',
            'options_external_content_sources_0_automatic_import_schedule'         => 'test_schedule',
            'options_external_content_sources_0_taxonomies'                        => '1',
            'options_external_content_sources_0_source_type'                       => 'test_source_type',
            'options_external_content_sources_0_source_json_file_path'             => 'test_json_file_path',
            'options_external_content_sources_0_source_typesense_api_key'          => 'test_api_key',
            'options_external_content_sources_0_source_typesense_protocol'         => 'test_protocol',
            'options_external_content_sources_0_source_typesense_host'             => 'test_host',
            'options_external_content_sources_0_source_typesense_port'             => 'test_port',
            'options_external_content_sources_0_source_typesense_collection'       => 'test_collection',
            'options_external_content_sources_0_taxonomies_0_from_schema_property' => 'test_from_schema_property',
            'options_external_content_sources_0_taxonomies_0_singular_name'        => 'test_singular_name',
            'options_external_content_sources_0_taxonomies_0_name'                 => 'test_name',
        ];
        $wpService  = new FakeWpService(['getOption' => $getOption, 'getOptions' => $getOptions]);

        $sourceConfigs = (new SourceConfigFactory($this->getSchemaDataConfig(), $wpService))->create();

        $this->assertEquals('test_post_type', $sourceConfigs[0]->getPostType());
        $this->assertEquals('test_schedule', $sourceConfigs[0]->getAutomaticImportSchedule());
        $this->assertEquals('test_source_type', $sourceConfigs[0]->getSourceType());
        $this->assertEquals('test_json_file_path', $sourceConfigs[0]->getSourceJsonFilePath());
        $this->assertEquals('test_api_key', $sourceConfigs[0]->getSourceTypesenseApiKey());
        $this->assertEquals('test_protocol', $sourceConfigs[0]->getSourceTypesenseProtocol());
        $this->assertEquals('test_host', $sourceConfigs[0]->getSourceTypesenseHost());
        $this->assertEquals('test_port', $sourceConfigs[0]->getSourceTypesensePort());
        $this->assertEquals('test_collection', $sourceConfigs[0]->getSourceTypesenseCollection());
        $this->assertEquals('test_from_schema_property', $sourceConfigs[0]->getTaxonomies()[0]->getFromSchemaProperty());
        $this->assertEquals('test_singular_name', $sourceConfigs[0]->getTaxonomies()[0]->getSingularName());
        $this->assertEquals('test_name', $sourceConfigs[0]->getTaxonomies()[0]->getName());
    }

    /**
     * @testdox returned $sourceConfig contains schema type
     */
    public function testReturnedSourceConfigContainsSchemaType()
    {
        $getOption  = fn($option, $default) => $option === 'options_external_content_sources' ? '1' : $default;
        $getOptions = fn($options) => ['options_external_content_sources_0_post_type' => 'test_post_type'];

        $wpService = new FakeWpService(['getOption' => $getOption, 'getOptions' => $getOptions]);

        $sourceConfigs = (new SourceConfigFactory($this->getSchemaDataConfig(), $wpService))->create();
        $this->assertEquals('test_schema_type', $sourceConfigs[0]->getSchemaType());
    }

    private function getSchemaDataConfig(): SchemaDataConfigInterface
    {
        return new class implements SchemaDataConfigInterface {
            public function featureIsEnabled(): bool
            {
                return true;
            }

            public function getEnabledPostTypes(): array
            {
                return ['test_post_type'];
            }

            public function tryGetSchemaTypeFromPostType(string $postType): ?string
            {
                return 'test_schema_type';
            }
        };
    }
}
