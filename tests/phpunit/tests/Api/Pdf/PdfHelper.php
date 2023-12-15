<?php

namespace Municipio\Tests\Api\Pdf;

use Municipio\Api\Pdf\PdfHelper;
use WP_Mock\Tools\TestCase;
use WP_Mock;
use Mockery;

class PdfHelperTest extends TestCase
{
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
