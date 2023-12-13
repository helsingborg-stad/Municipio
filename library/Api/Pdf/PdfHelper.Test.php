<?php

namespace Municipio\Api\Pdf\Test;

use Municipio\Api\Pdf\PdfHelper;
use WP_Mock\Tools\TestCase;

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
}
