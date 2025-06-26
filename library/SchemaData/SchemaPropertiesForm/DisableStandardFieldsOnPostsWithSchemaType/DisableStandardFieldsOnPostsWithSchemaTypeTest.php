<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\DisableStandardFieldsOnPostsWithSchemaType;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WP_Post_Type;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

class DisableStandardFieldsOnPostsWithSchemaTypeTest extends TestCase
{
    private TryGetSchemaTypeFromPostType|MockObject $schemaDataConfigService;
    private WpService $wpService;
    private DisableStandardFieldsOnPostsWithSchemaType $instance;

    protected function setUp(): void
    {
        // Mock TryGetSchemaTypeFromPostType
        $this->schemaDataConfigService = $this->getSchemaDataConfigServiceMock();

        // Mock AddAction, RegisterPostType, UnregisterPostType
        $this->wpService = new FakeWpService([
            'addAction'          => true,
            'unregisterPostType' => true,
            'registerPostType'   => new WP_Post_Type([]),
        ]);

        $this->instance = new DisableStandardFieldsOnPostsWithSchemaType(
            ['TestSchema'],
            ['editor', 'thumbnail'],
            $this->schemaDataConfigService,
            $this->wpService
        );
    }

    /**
     * @testdox addHooks registers the action 'registered_post_type'
     */
    public function testAddHooksRegistersAction()
    {
        $this->instance->addHooks();
        $this->assertEquals('registered_post_type', $this->wpService->methodCalls['addAction'][0][0]);
    }

    /**
     * @testdox disableStandardFields re-registers post type with given features disabled
     */
    public function testDisableStandardFieldsReRegistersPostTypeWithDisabledFeatures()
    {
        global $_wp_post_type_features;
        $postType               = 'test_post_type';
        $postTypeObject         = new WP_Post_Type([]);
        $_wp_post_type_features = [ $postType => [ 'editor' => true, 'thumbnail' => true, 'title' => true ] ];

        $this->schemaDataConfigService->method('tryGetSchemaTypeFromPostType')->with($postType)->willReturn('TestSchema');

        $this->instance->disableStandardFields($postType, $postTypeObject);

        $this->assertEquals($postType, $this->wpService->methodCalls['unregisterPostType'][0][0]);
        $this->assertEquals($postType, $this->wpService->methodCalls['registerPostType'][0][0]);
        $this->assertContains('title', $this->wpService->methodCalls['registerPostType'][0][1]['supports']);
    }

    /**
     * @testdox disableStandardFields does not re-register post type if schema type does not match
     */
    public function testDisableStandardFieldsDoesNotReRegistersPostTypeIfSchemaTypeDoesNotMatch()
    {
        global $_wp_post_type_features;
        $postType               = 'test_post_type';
        $postTypeObject         = new WP_Post_Type([]);
        $_wp_post_type_features = [ $postType => [ 'editor' => true, 'thumbnail' => true, 'title' => true ] ];

        $this->schemaDataConfigService->method('tryGetSchemaTypeFromPostType')->with($postType)->willReturn('DifferentSchema');

        $this->instance->disableStandardFields($postType, $postTypeObject);

        $this->assertArrayNotHasKey('unregisterPostType', $this->wpService->methodCalls);
        $this->assertArrayNotHasKey('registerPostType', $this->wpService->methodCalls);
    }


    private function getSchemaDataConfigServiceMock(): TryGetSchemaTypeFromPostType|MockObject
    {
        return $this->createMock(TryGetSchemaTypeFromPostType::class);
    }
}
