<?php

namespace Municipio\Tests\Api\Pdf;

use Municipio\Api\Pdf\PdfHelper;
use WP_Mock\Tools\TestCase;
use WP_Mock;
use Mockery;

class PdfHelperTest extends TestCase
{
    /**
     * @testdox getCoverFieldsForPostType returns an array with designated keys if any cover data is present.
     */
    public function testGetCoverFieldsForPostTypeReturnsCoverArrayIfHasData() {
        // Given 
        $pdfHelper = new PdfHelper();
        WP_Mock::userFunction('get_field', [ 'return' => "Test" ]);
        Mockery::mock('alias:' . \Municipio\Helper\Image::class)->shouldReceive('getImageAttachmentData')->andReturn(['src' => 'test']);
        
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
     */
    public function testGetCoverFieldsForPostTypeReturnsFalseIfNoDataAndNoCoverSelected() {
        // Given 
        $pdfHelper = new PdfHelper();
        WP_Mock::userFunction('get_field', [ 'return' => null ]);
        Mockery::mock('alias:' . \Municipio\Helper\Image::class)->shouldReceive('getImageAttachmentData')->andReturn(false);

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
        $pdfHelper = new PdfHelper();
        
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
        $pdfHelper = new PdfHelper();

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
            'times' => 1,
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
