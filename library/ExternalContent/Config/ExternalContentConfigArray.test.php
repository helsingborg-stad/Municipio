<?php

namespace Municipio\ExternalContent\Config;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ExternalContentConfigArrayTest extends TestCase
{
    /**
     * @testdox Class exists
     */
    public function testClassExists()
    {
        $this->assertTrue(class_exists('Municipio\ExternalContent\Config\ExternalContentConfigArray'));
    }

    /**
     * @testdox create returns an array
     */
    public function testCreateReturnsAnArray()
    {
        $factory = new ExternalContentConfigArray(new FakeWpService());
        $this->assertIsArray($factory->create());
    }

    /**
     * @testdox create returns an empty array if no rows are found
     */
    public function testCreateReturnsAnEmptyArrayIfNoRowsAreFound()
    {
        $getOption = fn($option, $default) => $option === 'external_content_sources' ? '0' : $default;
        $wpService = new FakeWpService(['getOption' => $getOption]);
        $factory   = new ExternalContentConfigArray($wpService);

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

        (new ExternalContentConfigArray($wpService))->create();

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
     * @testdox complete settings array can be returned
     */
    public function testCompleteSettingsArrayCanBeReturned()
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

        $factory = new ExternalContentConfigArray($wpService);

        $this->assertEquals([
            'post_type'                   => 'test_post_type',
            'automatic_import_schedule'   => 'test_schedule',
            'source_type'                 => 'test_source_type',
            'source_json_file_path'       => 'test_json_file_path',
            'source_typesense_api_key'    => 'test_api_key',
            'source_typesense_protocol'   => 'test_protocol',
            'source_typesense_host'       => 'test_host',
            'source_typesense_port'       => 'test_port',
            'source_typesense_collection' => 'test_collection',
            'taxonomies'                  => [
                [
                    'from_schema_property' => 'test_from_schema_property',
                    'singular_name'        => 'test_singular_name',
                    'name'                 => 'test_name',
                ],
            ],
        ], $factory->create()[0]);
    }
}
