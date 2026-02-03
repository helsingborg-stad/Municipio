<?php

namespace Municipio\Controller;

use Municipio\Controller\Navigation\MenuBuilderInterface;
use Municipio\Controller\Navigation\MenuDirector;
use Municipio\Helper\User\User;
use Municipio\Helper\SiteSwitcher\SiteSwitcherInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use AcfService\AcfService;

/**
 * Test case for Archive controller async attributes functionality
 *
 * @coversDefaultClass \Municipio\Controller\Archive
 */
class ArchiveTest extends TestCase
{
    /**
     * Create a mock Archive instance with all required dependencies
     */
    private function createArchive(): Archive
    {
        $menuBuilder = $this->createMock(MenuBuilderInterface::class);
        $menuDirector = $this->createMock(MenuDirector::class);

        // Use FakeWpService with required return values
        $wpService = new FakeWpService([
            'wpHead' => null,
            'wpFooter' => null,
            'applyFilters' => '',  // Return empty string for apply_filters
            'getPostTypeArchiveLink' => false,
            'removeQueryArg' => '',
            'getThemeMod' => 10,
            'getTerms' => [],
        ]);

        $acfService = $this->createMock(AcfService::class);
        $siteSwitcher = $this->createMock(SiteSwitcherInterface::class);
        $userHelper = $this->createMock(User::class);

        return new Archive(
            $menuBuilder,
            $menuDirector,
            $wpService,
            $acfService,
            $siteSwitcher,
            $userHelper
        );
    }

    /**
     * @covers ::getArchiveProperties
     */
    public function testGetArchivePropertiesReturnsObjectWhenCustomizationExists()
    {
        $archive = $this->createArchive();
        $customize = (object) [
            'archivePost' => [
                'style' => 'cards',
                'numberOfColumns' => 2,
            ],
        ];

        $reflection = new \ReflectionClass($archive);
        $method = $reflection->getMethod('getArchiveProperties');
        $method->setAccessible(true);

        $result = $method->invoke($archive, 'post', $customize);

        $this->assertIsObject($result);
        $this->assertEquals('cards', $result->style);
        $this->assertEquals(2, $result->numberOfColumns);
    }

    /**
     * @covers ::getArchiveProperties
     */
    public function testGetArchivePropertiesReturnsEmptyObjectWhenNoCustomization()
    {
        $archive = $this->createArchive();
        $customize = (object) [];

        $reflection = new \ReflectionClass($archive);
        $method = $reflection->getMethod('getArchiveProperties');
        $method->setAccessible(true);

        $result = $method->invoke($archive, 'post', $customize);

        $this->assertIsObject($result);
        $this->assertEmpty((array) $result);
    }

    /**
     * @covers ::getArchiveProperties
     */
    public function testGetArchivePropertiesWithDifferentPostTypes()
    {
        $archive = $this->createArchive();
        $customize = (object) [
            'archivePost' => ['style' => 'cards'],
            'archiveNews' => ['style' => 'list'],
            'archiveCustomPostType' => ['style' => 'grid'],
        ];

        $reflection = new \ReflectionClass($archive);
        $method = $reflection->getMethod('getArchiveProperties');
        $method->setAccessible(true);

        $postResult = $method->invoke($archive, 'post', $customize);
        $this->assertEquals('cards', $postResult->style);

        $newsResult = $method->invoke($archive, 'news', $customize);
        $this->assertEquals('list', $newsResult->style);

        $customResult = $method->invoke($archive, 'custom_post_type', $customize);
        $this->assertEquals('grid', $customResult->style);
    }

    /**
     * @covers ::camelCasePostTypeName
     */
    public function testCamelCasePostTypeName()
    {
        $archive = $this->createArchive();
        $reflection = new \ReflectionClass($archive);
        $method = $reflection->getMethod('camelCasePostTypeName');
        $method->setAccessible(true);

        $this->assertEquals('Post', $method->invoke($archive, 'post'));
        $this->assertEquals('CustomPostType', $method->invoke($archive, 'custom_post_type'));
        $this->assertEquals('MyCustomType', $method->invoke($archive, 'my-custom-type'));
        $this->assertEquals('MixedType', $method->invoke($archive, 'mixed_type-name'));
    }

    /**
     * @covers ::getArchiveTitle
     */
    public function testGetArchiveTitle()
    {
        $archive = $this->createArchive();
        $archiveProps = (object) ['heading' => 'Test Archive Title'];

        $reflection = new \ReflectionClass($archive);
        $method = $reflection->getMethod('getArchiveTitle');
        $method->setAccessible(true);

        $result = $method->invoke($archive, $archiveProps);

        $this->assertIsString($result);
        $this->assertEquals('Test Archive Title', $result);
    }

    /**
     * @covers ::getArchiveTitle
     */
    public function testGetArchiveTitleWithEmptyProps()
    {
        $archive = $this->createArchive();
        $archiveProps = (object) [];

        $reflection = new \ReflectionClass($archive);
        $method = $reflection->getMethod('getArchiveTitle');
        $method->setAccessible(true);

        $result = $method->invoke($archive, $archiveProps);

        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    /**
     * @covers ::getArchiveLead
     */
    public function testGetArchiveLead()
    {
        $archive = $this->createArchive();
        $archiveProps = (object) ['body' => 'Test archive description'];

        $reflection = new \ReflectionClass($archive);
        $method = $reflection->getMethod('getArchiveLead');
        $method->setAccessible(true);

        $result = $method->invoke($archive, $archiveProps);

        $this->assertIsString($result);
        $this->assertEquals('Test archive description', $result);
    }

    /**
     * @covers ::getArchiveLead
     */
    public function testGetArchiveLeadWithEmptyProps()
    {
        $archive = $this->createArchive();
        $archiveProps = (object) [];

        $reflection = new \ReflectionClass($archive);
        $method = $reflection->getMethod('getArchiveLead');
        $method->setAccessible(true);

        $result = $method->invoke($archive, $archiveProps);

        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    /**
     * @covers ::getAsyncAttributes
     */
    public function testGetAsyncAttributesReturnsEmptyArrayWhenProviderIsNull()
    {
        $archive = $this->createArchive();

        $reflection = new \ReflectionClass($archive);
        $method = $reflection->getMethod('getAsyncAttributes');
        $method->setAccessible(true);

        $result = $method->invoke($archive);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
