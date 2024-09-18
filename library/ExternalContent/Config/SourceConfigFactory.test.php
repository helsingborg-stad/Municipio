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
     * @testdox ACF json file exists
     */
    public function testAcfConfig()
    {
        $acfJsonFile = __DIR__ . '/../../AcfFields/json/external-content-settings.json';

        $this->assertFileExists($acfJsonFile);
    }

    /**
     * @testdox ACF json file contains repeater field with expected name
     */
    public function testAcfConfigContainsRepeaterField()
    {
        $jsonFileContents = file_get_contents(__DIR__ . '/../../AcfFields/json/external-content-settings.json');
        $json             = json_decode($jsonFileContents, true);

        $fields = $json[0]['fields'];

        $this->assertEquals('external_content_sources', $fields[0]['name']);
        $this->assertEquals('repeater', $fields[0]['type']);
    }

    /**
     * @testdox ACF json repeater field contains expected sub fields
     */
    public function testAcfConfigContainsExpectedSubFields()
    {
        $jsonFileContents = file_get_contents(__DIR__ . '/../../AcfFields/json/external-content-settings.json');
        $json             = json_decode($jsonFileContents, true);

        $fields = $json[0]['fields'];

        $subFieldNames = array_map(fn($subField) => $subField['name'], $fields[0]['sub_fields']);

        $this->assertContains('post_type', $subFieldNames);
        $this->assertContains('source_type', $subFieldNames);
        $this->assertContains('source_json_file_path', $subFieldNames);
        $this->assertContains('source_typesense_api_key', $subFieldNames);
        $this->assertContains('source_typesense_protocol', $subFieldNames);
        $this->assertContains('source_typesense_host', $subFieldNames);
        $this->assertContains('source_typesense_port', $subFieldNames);
        $this->assertContains('source_typesense_collection', $subFieldNames);
        $this->assertContains('automatic_import_schedule', $subFieldNames);
        $this->assertContains('taxonomies', $subFieldNames);
    }

    /**
     * @testdox ACF json taxonomies field contains expected sub fields
     */
    public function testAcfConfigContainsExpectedTaxonomiesSubFields()
    {
        $jsonFileContents        = file_get_contents(__DIR__ . '/../../AcfFields/json/external-content-settings.json');
        $json                    = json_decode($jsonFileContents, true);
        $fields                  = $json[0]['fields'];
        $subFields               = $fields[0]['sub_fields'];
        $taxonomiesField         = array_values(array_filter($subFields, fn($subField) => $subField['name'] === 'taxonomies'));
        $taxonomiesSubFieldNames = array_map(fn($subField) => $subField['name'], $taxonomiesField[0]['sub_fields']);

        $this->assertContains('from_schema_property', $taxonomiesSubFieldNames);
        $this->assertContains('singular_name', $taxonomiesSubFieldNames);
        $this->assertContains('name', $taxonomiesSubFieldNames);
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
