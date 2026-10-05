<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Config;

use AcfService\Contracts\GetField;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class EasyReadConfigTest extends TestCase
{
    #[TestDox('preserves the former plugin data keys and returns configured post types')]
    public function testPreservesLegacyDataKeys(): void
    {
        $acfService = $this->createMock(GetField::class);
        $acfService->expects(static::once())
            ->method('getField')
            ->with('easy_reading_posttypes', 'option')
            ->willReturn(['page', 'post', 42]);

        $config = new EasyReadConfig($acfService);

        static::assertSame('easy_reading_select', $config->enabledField());
        static::assertSame('easy_reading_content', $config->contentField());
        static::assertSame('easy_reading_posttypes', $config->postTypesField());
        static::assertSame('readable', $config->readableQueryParameter());
        static::assertSame(['page', 'post'], $config->enabledPostTypes());
    }
}
