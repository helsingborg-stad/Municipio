<?php

namespace Municipio\ImageConvert;

use PHPUnit\Framework\TestCase;
use Municipio\ImageConvert\NormalizeImageSize;
use WpService\Implementations\FakeWpService;
use Municipio\ImageConvert\Config\ImageConvertConfig;

class NormalizeImageSizeTest extends TestCase
{
    /**
     * @testdox Caps dimensions to the specified limit and scales proportionally if needed.
     *
     * @dataProvider normalizeSizeCapProvider
     */
    public function testNormalizeSizeCap($size, $expected): void
    {
        $wpService          = new FakeWpService();
        $configService      = new ImageConvertConfig(
            $wpService
        );
        $normalizeImageSize = new NormalizeImageSize(
            $wpService,
            $configService
        );

        $result = $normalizeImageSize->normalizeSizeCap(
            $size,
            2500
        );
        $this->assertEquals($expected, $result);
    }

    /**
     * Test data provider for testNormalizeSizeCap.
     */
    private function normalizeSizeCapProvider()
    {
        return [
            [[3000, 4000], [1875, 2500]],
            [[false, 4000], [false, 2500]],
            [[3000, false], [2500, false]],
            [[1000, 1000], [1000, 1000]],
            [[false, false], [false, false]]
        ];
    }
}
