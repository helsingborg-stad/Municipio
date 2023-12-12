<?php

namespace Municipio\Api\Pdf\Test;

use WP_Mock;
use WP_Mock\Tools\TestCase;

class PdfHelperGetThemeModsTest extends TestCase
{
    public function testReturnsArray()
    {
        // Given
        $pdfHelper = new \Municipio\Api\Pdf\PdfHelper();

        // When
        $result = $pdfHelper->getThemeMods();

        // Then
        $this->assertIsArray($result);
    }

    public function testReturnsThemeMods()
    {
        // Given
        WP_Mock::userFunction('get_theme_mods', [
            'times' => 1,
            'return' => ['modName' => 'modValue']
        ]);

        $pdfHelper = new \Municipio\Api\Pdf\PdfHelper();
        $this->assertEquals(['modName' => 'modValue'], $pdfHelper->getThemeMods());
    }

    public function testReturnsArrayEvenIfGetThemeModsDoesNot()
    {
        WP_Mock::userFunction('get_theme_mods', [ 'times' => 1, 'return' => null ]);
        $pdfHelper = new \Municipio\Api\Pdf\PdfHelper();
        $this->assertIsArray($pdfHelper->getThemeMods());
    }
}
