<?php

namespace Municipio\Tests\Api\Pdf;

use Municipio\Api\Pdf\PdfHelper;
use WP_Mock\Tools\TestCase;
use WP_Mock;
use Mockery;
use Municipio\Helper\FileConverters\FileConverterInterface;

/**
 * Class PdfHelperTest
 */
class PdfHelperTest extends TestCase
{
    /**
     * @testdox getFonts Returns default values if no custom font files or styles.
     * @runInSeparateProcess
     */
    public function testGetFontsReturnsDefaultArrayIfNoCustomFontsAreAvailable()
    {
        // Given
        $pdfHelper         = new PdfHelper();
        $woffConverterMock = Mockery::mock(FileConverterInterface::class);
        $wp_query          = Mockery::mock('WP_Query');

        // When
        $result = $pdfHelper->getFonts([], $woffConverterMock);

        // Then
        $this->assertArrayHasKey('heading', $result);
        $this->assertArrayHasKey('base', $result);
    }

    /**
     * @testdox getFonts Returns the default array containing two arrays without the src key.
     * @runInSeparateProcess
     */
    public function testGetFontsReturnsArrayWithDefaultValuesIfCustomFontsDoNotMatch()
    {
        // Given
        $pdfHelper         = new PdfHelper();
        $woffConverterMock = Mockery::mock(FileConverterInterface::class);
        $woffConverterMock->shouldReceive('convert')->andReturn('test');
        $mockPosts   = [$this->mockPost(['ID' => 1, 'post_title' => 'notMatching'])];
        $wpQueryMock = Mockery::mock('overload:WP_Query');
        $wpQueryMock->shouldReceive('__construct')->times(1)->withAnyArgs()->andSet('posts', $mockPosts);

        // When
        $result = $pdfHelper->getFonts([
            'typography_heading' => ['font-family' => 'test'],
            'typography_base'    => ['font-family' => 'test']
        ], $woffConverterMock);

        // Then
        $this->assertEmpty($result['heading']['src']);
        $this->assertEmpty($result['base']['src']);
    }

    /**
     * @testdox getFonts Returns an array with arrays containing src
     * if custom fonts exists and have the same post_title as styles headings or base font.
     * @runInSeparateProcess
     */
    public function testGetFontsReturnsArrayWithCustomFontsUrlIfCustomFontsExistsAndMatchesStyles()
    {
        // Given
        $pdfHelper         = new PdfHelper();
        $woffConverterMock = Mockery::mock(FileConverterInterface::class);
        $woffConverterMock->shouldReceive('convert')->andReturn('test');
        $mockPosts   = [$this->mockPost(['ID' => 1, 'post_title' => 'test'])];
        $wpQueryMock = Mockery::mock('overload:WP_Query');
        $wpQueryMock->shouldReceive('__construct')->times(1)->withAnyArgs()->andSet('posts', $mockPosts);

        // When
        $result = $pdfHelper->getFonts([
            'typography_heading' => ['font-family' => 'test'],
            'typography_base'    => ['font-family' => 'test']
        ], $woffConverterMock);

        // Then
        $this->assertNotEmpty($result['heading']['src']);
        $this->assertNotEmpty($result['base']['src']);
    }

    /**
     * @testdox getCoverFieldsForPostType returns an array with designated keys if any cover data is present.
     * @runInSeparateProcess
     */
    public function testGetCoverFieldsForPostTypeReturnsCoverArrayIfHasData()
    {
        // Given
        $pdfHelper = new PdfHelper();
        WP_Mock::userFunction('get_field', [ 'return' => "Test" ]);
        Mockery::mock('alias:' . \Municipio\Helper\Image::class)
            ->shouldReceive('getImageAttachmentData')
            ->andReturn(['src' => 'test']);

        // When
        $result = $pdfHelper->getCoverFieldsForPostType();

        // Then
        $this->assertArrayHasKey('heading', $result);
        $this->assertArrayHasKey('introduction', $result);
        $this->assertArrayHasKey('cover', $result);
        $this->assertArrayHasKey('emblem', $result);
    }

    /**
     * @testdox getCoverFieldsForPostType returns false if no cover data is present.
     * @runInSeparateProcess
     */
    public function testGetCoverFieldsForPostTypeReturnsFalseIfNoDataAndNoCoverSelected()
    {
        // Given
        $pdfHelper = new PdfHelper();
        WP_Mock::userFunction('get_field', [ 'return' => null ]);
        Mockery::mock('alias:' . \Municipio\Helper\Image::class)
            ->shouldReceive('getImageAttachmentData')
            ->andReturn(false);

        // When
        $result = $pdfHelper->getCoverFieldsForPostType();

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox systemHasSuggestedDependencies returns false if extension GD is not loaded.
     */
    public function testSystemHasSuggestedDependenciesReturnsFalseIfMissingGD()
    {
        // Given
        $mockExtensionLoaded = \tad\FunctionMocker\FunctionMocker::replace('extension_loaded', false);
        $pdfHelper           = new PdfHelper();

        // When
        $result = $pdfHelper->systemHasSuggestedDependencies();

        // Then
        $this->assertFalse($result);
        $mockExtensionLoaded->wasCalledWithTimes(['gd'], 1);
    }

    /**
     * @testdox systemHasSuggestedDependencies returns true if not missing dependencies.
     */
    public function testSystemHasSuggestedDependenciesReturnsTrueIfNotMissingDependencies()
    {
        // Given
        $mockExtensionLoaded = \tad\FunctionMocker\FunctionMocker::replace('extension_loaded', true);
        $pdfHelper           = new PdfHelper();

        // When
        $pdfHelper->systemHasSuggestedDependencies();

        // Then
        $mockExtensionLoaded->wasCalledWithTimes(['gd'], 1);
        $this->assertTrue($pdfHelper->systemHasSuggestedDependencies());
    }

    public function testGetCoverInvokesGetCoverFieldsForPostType()
    {
        // Given
        $pdfHelper = $this->getPdfHelper('default');

        // When
        $cover = $pdfHelper->getCover([]);

        // Then
        $this->assertEquals(['default'], $cover);
    }

    public function testGetCoverDefaultsToFirstPostType()
    {
        // Given
        $postTypes = ['firstPostType', 'secondPostType'];
        $pdfHelper = $this->getPdfHelper('firstPostType');

        // When
        $cover = $pdfHelper->getCover($postTypes);

        // Then
        $this->assertEquals(['firstPostType'], $cover);
    }

    public function testGetCoverSanitizesPostType()
    {
        // Given
        $postTypes = [null];
        $pdfHelper = $this->getPdfHelper('default');

        // When
        $cover = $pdfHelper->getCover($postTypes);

        // Then
        $this->assertEquals(['default'], $cover);
    }

    public function testGetCoverUsesFirstValidPostTypeFromInput()
    {
        // Given
        $postTypes = [null, 'secondPostType'];
        $pdfHelper = $this->getPdfHelper('secondPostType');

        // When
        $cover = $pdfHelper->getCover($postTypes);

        // Then
        $this->assertEquals(['secondPostType'], $cover);
    }

    public function testGetThemeModsReturnsArray()
    {
        // Given
        $pdfHelper = new \Municipio\Api\Pdf\PdfHelper();

        // When
        $result = $pdfHelper->getThemeMods();

        // Then
        $this->assertIsArray($result);
    }

    public function testGetThemeModsReturnsThemeMods()
    {
        // Given
        WP_Mock::userFunction('get_theme_mods', [
            'times'  => 1,
            'return' => ['modName' => 'modValue']
        ]);

        $pdfHelper = new \Municipio\Api\Pdf\PdfHelper();
        $this->assertEquals(['modName' => 'modValue'], $pdfHelper->getThemeMods());
    }

    public function testGetThemeModsReturnsArrayEvenIfGetThemeModsDoesNot()
    {
        WP_Mock::userFunction('get_theme_mods', [ 'times' => 1, 'return' => null ]);
        $pdfHelper = new \Municipio\Api\Pdf\PdfHelper();
        $this->assertIsArray($pdfHelper->getThemeMods());
    }

    /**
     * @return PdfHelper
     */
    private function getPdfHelper($postType)
    {
        return Mockery::mock(PdfHelper::class)
            ->makePartial()
            ->shouldReceive('getCoverFieldsForPostType')
            ->once()
            ->with($postType)
            ->andReturnUsing(fn ($input) => [$input])
            ->getMock();
    }
}
