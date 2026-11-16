<?php

declare(strict_types=1);

namespace Municipio\Controller\Header\Helper;

use Municipio\Controller\Header\Header;
use PHPUnit\Framework\TestCase;

class HeaderComponentsTest extends TestCase
{
    public function testHasReturnsTrueWhenComponentIsInAnyHeader(): void
    {
        $headerComponents = new HeaderComponents([
            'upper' => $this->createHeaderWithComponents(['logotype']),
            'lower' => $this->createHeaderWithComponents(['brand-text']),
        ]);

        static::assertTrue($headerComponents->has('logotype'));
        static::assertTrue($headerComponents->has('brand-text'));
    }

    public function testHasReturnsFalseWhenComponentIsMissing(): void
    {
        $headerComponents = new HeaderComponents([
            'upper' => $this->createHeaderWithComponents(['logotype']),
        ]);

        static::assertFalse($headerComponents->has('brand-text'));
    }

    public function testHasIgnoresNonHeaderValues(): void
    {
        $headerComponents = new HeaderComponents(['upper' => ['brand-text' => []]]);

        static::assertFalse($headerComponents->has('brand-text'));
    }

    private function createHeaderWithComponents(array $components): Header
    {
        $header = $this->createMock(Header::class);
        $header->method('getMenuItems')->willReturn(array_fill_keys($components, new \stdClass()));

        return $header;
    }
}
