<?php

namespace Municipio\Tests\Api\Pdf;

use Mockery;
use Municipio\Api\Pdf\PdfHelperInterface;
use WP_Mock;
use WP_Mock\Tools\TestCase;

/**
 * Class PdfGeneratorTest
 * @group wp_mock
 */
class PdfGeneratorTest extends TestCase
{
    /**
     * @testdox displayMissingSuggestedDependenciesNotices does nothing if not on settings page.
     */
    public function testDisplayMissingSuggestedDependenciesNoticesDoesNothingIfNotOnSettingsPage()
    {
        // Given
        $pdfHelper = Mockery::mock(PdfHelperInterface::class);
        $pdfHelper->shouldReceive('systemHasSuggestedDependencies')->andReturn(true);
        WP_Mock::userFunction('wp_admin_notice', ['times' => 0]);
        WP_Mock::userFunction('is_admin')->andReturn(true);
        $_GET['page'] = 'foo';

        // When
        $pdfGenerator = new \Municipio\Api\Pdf\PdfGenerator($pdfHelper);
        $pdfGenerator->displayMissingSuggestedDependenciesNotices();

        // Then
        $this->assertConditionsMet();
    }

    /**
     * @testdox displayMissingSuggestedDependenciesNotices does nothing if has all dependencies.
     */
    public function testDisplayMissingSuggestedDependenciesNoticesDoesNothingIfHasAllDependencies()
    {
        // Given
        $pdfHelper = Mockery::mock(PdfHelperInterface::class);
        $pdfHelper->shouldReceive('systemHasSuggestedDependencies')->andReturn(true);
        WP_Mock::userFunction('wp_admin_notice', ['times' => 0]);
        WP_Mock::userFunction('is_admin')->andReturn(true);
        $_GET['page'] = 'pdf-generator-settings';

        // When
        $pdfGenerator = new \Municipio\Api\Pdf\PdfGenerator($pdfHelper);
        $pdfGenerator->displayMissingSuggestedDependenciesNotices();

        // Then
        $this->assertConditionsMet();
    }

    /**
     * @testdox displayMissingSuggestedDependenciesNotices shows notices if missing dependencies.
     */
    public function testDisplayMissingSuggestedDependenciesNoticesShowNoticesIfMissingDependencies()
    {
        // Given
        $pdfHelper = Mockery::mock(PdfHelperInterface::class);
        $pdfHelper->shouldReceive('systemHasSuggestedDependencies')->andReturn(false);
        WP_Mock::userFunction('wp_admin_notice', ['times' => 1]);
        WP_Mock::userFunction('is_admin')->andReturn(true);
        $_GET['page'] = 'pdf-generator-settings';

        // When
        $pdfGenerator = new \Municipio\Api\Pdf\PdfGenerator($pdfHelper);
        $pdfGenerator->displayMissingSuggestedDependenciesNotices();

        // Then
        $this->assertConditionsMet();
    }
}
