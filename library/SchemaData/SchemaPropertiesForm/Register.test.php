<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use AcfService\Implementations\FakeAcfService;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WP_Screen;
use WpService\Implementations\FakeWpService;

class RegisterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        unset($_GET['post']);
    }

    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated(): void
    {
        $acfService                   = new FakeAcfService();
        $wpService                    = new FakeWpService();
        $getAcfFieldGroupBySchemaType = $this->getAcfFieldGroupBySchemaType();
        $configService                = $this->getConfigService();

        $register = new Register($acfService, $wpService, $getAcfFieldGroupBySchemaType, $configService);

        $this->assertInstanceOf(Register::class, $register);
    }

    /**
     * @testdox attaches the current_screen action
     */
    public function testAddHooksCallsAddAction(): void
    {
        $acfService                   = new FakeAcfService();
        $wpService                    = new FakeWpService(['addAction' => true]);
        $getAcfFieldGroupBySchemaType = $this->getAcfFieldGroupBySchemaType();
        $configService                = $this->getConfigService();
        $register                     = new Register($acfService, $wpService, $getAcfFieldGroupBySchemaType, $configService);

        $register->addHooks();

        $this->assertEquals('current_screen', $wpService->methodCalls['addAction'][0][0]);
    }

    /**
     * @testdox resgister() calls addLocalFieldGroup() with the correct parameters
     */
    public function testRegisterCallsAddLocalFieldGroup(): void
    {
        $wpScreen                     = new WP_Screen();
        $wpScreen->base               = 'post';
        $wpScreen->post_type          = 'test_post_type';
        $wpService                    = new FakeWpService(['getCurrentScreen' => $wpScreen]);
        $acfService                   = new FakeAcfService(['addLocalFieldGroup' => true]);
        $getAcfFieldGroupBySchemaType = $this->getAcfFieldGroupBySchemaType();
        $getAcfFieldGroupBySchemaType->method('getAcfFieldGroup')->with('test_schema_type')->willReturn(['test_form']);
        $configService = $this->getConfigService();
        $configService->method('tryGetSchemaTypeFromPostType')->with('test_post_type')->willReturn('test_schema_type');
        $register = new Register($acfService, $wpService, $getAcfFieldGroupBySchemaType, $configService);

        $register->register();

        $this->assertCount(1, $acfService->methodCalls['addLocalFieldGroup']);
        $this->assertEquals(['test_form'], $acfService->methodCalls['addLocalFieldGroup'][0][0]);
    }

    /**
     * @testdox register() does not call addLocalFieldGroup() if the screen is not a post from external source
     */
    public function testRegisterDoesNotCallAddLocalFieldGroupIfNotPost(): void
    {
        $_GET['post'] = '123';

        $wpScreen                     = new WP_Screen();
        $wpScreen->base               = 'post';
        $wpScreen->post_type          = 'test_post_type';
        $wpService                    = new FakeWpService(['getCurrentScreen' => $wpScreen, 'getPostMeta' => 'originId']);
        $acfService                   = new FakeAcfService(['addLocalFieldGroup' => true]);
        $getAcfFieldGroupBySchemaType = $this->getAcfFieldGroupBySchemaType();
        $getAcfFieldGroupBySchemaType->method('getAcfFieldGroup')->with('test_schema_type')->willReturn(['test_form']);
        $configService = $this->getConfigService();
        $configService->method('tryGetSchemaTypeFromPostType')->with('test_post_type')->willReturn('test_schema_type');
        $register = new Register($acfService, $wpService, $getAcfFieldGroupBySchemaType, $configService);

        $register->register();

        $this->assertArrayNotHasKey('addLocalFieldGroup', $acfService->methodCalls);
    }

    private function getConfigService(): TryGetSchemaTypeFromPostType|MockObject
    {
        return $this->createMock(TryGetSchemaTypeFromPostType::class);
    }

    private function getAcfFieldGroupBySchemaType(): GetAcfFieldGroupBySchemaTypeInterface|MockObject
    {
        return $this->createMock(GetAcfFieldGroupBySchemaTypeInterface::class);
    }
}
