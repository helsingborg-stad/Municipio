<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

use AcfService\Contracts\AddLocalFieldGroup;
use Municipio\SchemaData\Config\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\PostObject\Factory\PostObjectFromWpPostFactoryInterface;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory\FormFactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WP_Post;
use WP_Screen;
use WpService\Implementations\FakeWpService;

class RegisterTest extends TestCase
{
    private function getAcfService(): AddLocalFieldGroup|MockObject
    {
        return $this->createMock(AddLocalFieldGroup::class);
    }

    private function getConfigService(): TryGetSchemaTypeFromPostType|MockObject
    {
        $mock = $this->createMock(TryGetSchemaTypeFromPostType::class);
        $mock->method('tryGetSchemaTypeFromPostType')->willReturn('Store');
        return $mock;
    }

    private function getFormFactory(): FormFactoryInterface|MockObject
    {
        $mock = $this->createMock(FormFactoryInterface::class);
        $mock->method('createForm')->willReturn(['fields' => []]);
        return $mock;
    }

    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $sut = new Register(
            $this->getAcfService(),
            new FakeWpService(),
            $this->getConfigService(),
            $this->getFormFactory(),
            $this->getPostObjectFactory()
        );
        $this->assertInstanceOf(Register::class, $sut);
    }

    private function getPostObjectFactory(): PostObjectFromWpPostFactoryInterface|MockObject
    {
        return $this->createMock(PostObjectFromWpPostFactoryInterface::class);
    }

    /**
     * @testdox addHooks adds action to current_screen
     */
    public function testAddHooksAddsAction()
    {
        $wpService = new FakeWpService(['addAction' => true]);
        $sut       = new Register(
            $this->getAcfService(),
            $wpService,
            $this->getConfigService(),
            $this->getFormFactory(),
            $this->getPostObjectFactory()
        );

        $sut->addHooks();

        $this->assertEquals('current_screen', $wpService->methodCalls['addAction'][0][0]);
    }

    /**
     * @testdox register does not call addLocalFieldGroup if shouldRegisterForm returns false
     */
    public function testRegisterDoesNotCallAddLocalFieldGroupIfShouldRegisterFormFalse()
    {
        $acfService = $this->getAcfService();
        $acfService->expects($this->never())->method('addLocalFieldGroup');
        $wpScreen            = new WP_Screen();
        $wpScreen->base      = 'edit';
        $wpScreen->post_type = 'post';
        $wpService           = new FakeWpService([
            'getCurrentScreen' => $wpScreen,
            'getPostMeta'      => '',
        ]);
        $configService       = $this->getConfigService();
        $formFactory         = $this->getFormFactory();

        $sut = new Register($acfService, $wpService, $configService, $formFactory, $this->getPostObjectFactory());
        $sut->register();
    }

    /**
     * @testdox register calls addLocalFieldGroup if shouldRegisterForm returns true
     */
    public function testRegisterCallsAddLocalFieldGroupIfShouldRegisterFormTrue()
    {
        $acfService = $this->getAcfService();
        $acfService->expects($this->once())->method('addLocalFieldGroup');

        $screen            = new WP_Screen();
        $screen->base      = 'post';
        $screen->post_type = 'post';
        $wpService         = new FakeWpService([
            'getCurrentScreen' => $screen,
            'getPostMeta'      => '',
            'getPost'          => new WP_Post([])
        ]);
        $configService     = $this->getConfigService();
        $formFactory       = $this->getFormFactory();

        $sut = new Register($acfService, $wpService, $configService, $formFactory, $this->getPostObjectFactory());
        $sut->register();
    }
}
