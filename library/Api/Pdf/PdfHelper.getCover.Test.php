<?php

namespace Municipio\Api\Pdf\Test;

use Mockery;
use Municipio\Api\Pdf\PdfHelper;
use WP_Mock\Tools\TestCase;

class PdfHelperGetCoverTest extends TestCase
{
    public function testInvokesGetCoverFieldsForPostType()
    {
        // Given
        $pdfHelper = $this->getPdfHelper('default');

        // When
        $cover = $pdfHelper->getCover([]);

        // Then
        $this->assertEquals(['default'], $cover);
    }

    public function testDefaultsToFirstPostType()
    {
        // Given
        $postTypes = ['firstPostType', 'secondPostType'];
        $pdfHelper = $this->getPdfHelper('firstPostType');

        // When
        $cover = $pdfHelper->getCover($postTypes);

        // Then
        $this->assertEquals(['firstPostType'], $cover);
    }

    public function testSanitizesPostType()
    {
        // Given
        $postTypes = [null];
        $pdfHelper = $this->getPdfHelper('default');

        // When
        $cover = $pdfHelper->getCover($postTypes);

        // Then
        $this->assertEquals(['default'], $cover);
    }

    public function testUsesFirstValidPostTypeFromInput()
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
