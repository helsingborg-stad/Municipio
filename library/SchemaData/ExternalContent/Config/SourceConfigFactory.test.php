<?php

namespace Municipio\SchemaData\ExternalContent\Config;

use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;
use Municipio\SchemaData\ExternalContent\Filter\FilterDefinition\Contracts\Enums\Operator;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class SourceConfigFactoryTest extends TestCase
{
    /**
     * @testdox Class exists
     */
    public function testClassExists()
    {
        $this->assertTrue(class_exists('Municipio\SchemaData\ExternalContent\Config\SourceConfigFactory'));
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
        $json = $this->getAcfFields();

        $fields = $json[0]['fields'];

        $this->assertEquals('external_content_sources', $fields[0]['name']);
        $this->assertEquals('repeater', $fields[0]['type']);
    }

    /**
     * @testdox ACF json repeater field contains expected sub fields
     */
    public function testAcfConfigContainsExpectedSubFields()
    {
        $json = $this->getAcfFields();

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
        $this->assertContains('rules', $subFieldNames);
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
        $getOptions = fn($options) =>  $this->getTestAcfData();
        $wpService  = new FakeWpService(['getOption' => $getOption, 'getOptions' => $getOptions]);

        @(new SourceConfigFactory($this->getSchemaDataConfig(), $wpService))->create();

        $this->assertEquals([
            'options_external_content_sources_0_post_type',
            'options_external_content_sources_0_automatic_import_schedule',
            'options_external_content_sources_0_source_type',
            'options_external_content_sources_0_source_json_file_path',
            'options_external_content_sources_0_source_typesense_api_key',
            'options_external_content_sources_0_source_typesense_protocol',
            'options_external_content_sources_0_source_typesense_host',
            'options_external_content_sources_0_source_typesense_port',
            'options_external_content_sources_0_source_typesense_collection',
            'options_external_content_sources_0_rules',

        ], $wpService->methodCalls['getOptions'][0][0]);

        $this->assertEquals([
            'options_external_content_sources_0_rules_0_property_path',
            'options_external_content_sources_0_rules_0_operator',
            'options_external_content_sources_0_rules_0_value',

        ], $wpService->methodCalls['getOptions'][1][0]);
    }

    /**
     * @testdox array of SourceConfigInterface objects are returned
     */
    public function testReturnsExpectedSourceConfigObjects()
    {
        $getOption = fn($option, $default) => $option === 'options_external_content_sources' ? '1' : $default;
        $wpService = new FakeWpService(['getOption' => $getOption, 'getOptions' => fn($options) => $this->getTestAcfData()]);

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
        $this->assertEquals('test_property_path', $sourceConfigs[0]->getFilterDefinition()->getRuleSets()[0]->getRules()[0]->getPropertyPath());
        $this->assertEquals(Operator::EQUALS, $sourceConfigs[0]->getFilterDefinition()->getRuleSets()[0]->getRules()[0]->getOperator());
        $this->assertEquals('test_value', $sourceConfigs[0]->getFilterDefinition()->getRuleSets()[0]->getRules()[0]->getValue());
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

    private function getAcfFields(): array
    {
        return json_decode(file_get_contents(__DIR__ . '/../../AcfFields/json/external-content-settings.json'), true);
    }

    private function getTestAcfData(): array
    {
        return [
            'options_external_content_sources_0_post_type'                   => 'test_post_type',
            'options_external_content_sources_0_automatic_import_schedule'   => 'test_schedule',
            'options_external_content_sources_0_taxonomies'                  => '1',
            'options_external_content_sources_0_source_type'                 => 'test_source_type',
            'options_external_content_sources_0_source_json_file_path'       => 'test_json_file_path',
            'options_external_content_sources_0_source_typesense_api_key'    => 'test_api_key',
            'options_external_content_sources_0_source_typesense_protocol'   => 'test_protocol',
            'options_external_content_sources_0_source_typesense_host'       => 'test_host',
            'options_external_content_sources_0_source_typesense_port'       => 'test_port',
            'options_external_content_sources_0_source_typesense_collection' => 'test_collection',
            'options_external_content_sources_0_rules'                       => '1',
            'options_external_content_sources_0_rules_0_property_path'       => 'test_property_path',
            'options_external_content_sources_0_rules_0_operator'            => 'test_operator',
            'options_external_content_sources_0_rules_0_value'               => 'test_value',
        ];
    }
}
