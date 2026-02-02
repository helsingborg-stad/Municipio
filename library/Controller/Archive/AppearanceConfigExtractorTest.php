<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use PHPUnit\Framework\TestCase;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;

class AppearanceConfigExtractorTest extends TestCase
{
    public function testExtractReturnsAllAvailableData(): void
    {
        $appearanceConfig = $this->createMock(AppearanceConfigInterface::class);
        $appearanceConfig->method('getDateSource')->willReturn('modified_date');
        $appearanceConfig->method('getDateFormat')->willReturn((object)['value' => 'date']);
        $appearanceConfig->method('getNumberOfColumns')->willReturn(3);

        $extractor = new AppearanceConfigExtractor($appearanceConfig);
        $result = $extractor->extract();

        $this->assertEquals([
            'dateSource' => 'modified_date',
            'dateFormat' => 'date',
            'numberOfColumns' => 3,
        ], $result);
    }

    public function testExtractHandlesMissingMethods(): void
    {
        $appearanceConfig = $this->createMock(AppearanceConfigInterface::class);

        $extractor = new AppearanceConfigExtractor($appearanceConfig);
        $result = $extractor->extract();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testExtractHandlesNullDateFormat(): void
    {
        $appearanceConfig = $this->createMock(AppearanceConfigInterface::class);
        $appearanceConfig->method('getDateFormat')->willReturn(null);

        $extractor = new AppearanceConfigExtractor($appearanceConfig);
        $result = $extractor->extract();

        $this->assertEquals(['dateFormat' => 'date-time'], $result);
    }
}
