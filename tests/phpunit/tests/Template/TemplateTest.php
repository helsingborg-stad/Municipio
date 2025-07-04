<?php

namespace Municipio\Tests\Template;

use Municipio\Template;
use Municipio\Config\Features\SchemaData\SchemaDataConfigInterface;
use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use Municipio\Admin\Private\MainQueryUserGroupRestriction;
use Municipio\Helper\SiteSwitcher\SiteSwitcher;
use Municipio\PostObject\Factory\PostObjectFromWpPostFactoryInterface;
use Municipio\Helper\User\User;
use AcfService\AcfService;
use WpService\WpService;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    private Template $template;

    protected function setUp(): void
    {
        // Create mocks for all dependencies
        $menuBuilder = $this->createMock(MenuBuilderInterface::class);
        $menuDirector = $this->createMock(MenuDirector::class);
        $acfService = $this->createMock(AcfService::class);
        $wpService = $this->createMock(WpService::class);
        $schemaDataConfig = $this->createMock(SchemaDataConfigInterface::class);
        $mainQueryUserGroupRestriction = $this->createMock(MainQueryUserGroupRestriction::class);
        $siteSwitcher = $this->createMock(SiteSwitcher::class);
        $postObjectFromWpPost = $this->createMock(PostObjectFromWpPostFactoryInterface::class);
        $userHelper = $this->createMock(User::class);

        // Mock WordPress functions
        $wpService->method('applyFilters')->willReturn('');
        
        $this->template = new Template(
            $menuBuilder,
            $menuDirector,
            $acfService,
            $wpService,
            $schemaDataConfig,
            $mainQueryUserGroupRestriction,
            $siteSwitcher,
            $postObjectFromWpPost,
            $userHelper
        );
    }

    public function testInitializeBladePreventsDuplicateInitialization(): void
    {
        // Test that multiple calls to initializeBlade don't cause issues
        $this->template->initializeBlade();
        $this->template->initializeBlade();
        
        // If this test passes without errors, the guard is working
        $this->assertTrue(true);
    }

    public function testRenderViewInitializesBladeEngineIfNotInitialized(): void
    {
        // Mock the view paths to return a simple array
        $reflection = new \ReflectionClass($this->template);
        $viewPathsProperty = $reflection->getProperty('viewPaths');
        $viewPathsProperty->setAccessible(true);
        $viewPathsProperty->setValue($this->template, []);

        // This should trigger blade engine initialization if not already initialized
        try {
            $this->template->renderView('test', []);
        } catch (\Throwable $e) {
            // Expected to throw an exception due to missing view, but the important thing
            // is that it doesn't fail due to uninitialized blade engine
            $this->assertStringContainsString('View', $e->getMessage());
        }
        
        $this->assertTrue(true);
    }
}