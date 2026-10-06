<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Content;

use AcfService\Contracts\GetField;
use Municipio\EasyRead\Config\EasyReadConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AcfAlternativeContentRepositoryTest extends TestCase
{
    #[TestDox('reads alternative content using the existing Easy Read field names')]
    public function testReadsAlternativeContent(): void
    {
        $config = $this->createMock(EasyReadConfigInterface::class);
        $config->method('enabledField')->willReturn('easy_reading_select');
        $config->method('contentField')->willReturn('easy_reading_content');

        $acfService = $this->createMock(GetField::class);
        $acfService->method('getField')->willReturnMap([
            ['easy_reading_select', 123, true],
            ['easy_reading_content', 123, '<p>Plain-language content</p>'],
        ]);

        $repository = new AcfAlternativeContentRepository($acfService, $config);

        static::assertTrue($repository->hasAlternative(123));
        static::assertSame('<p>Plain-language content</p>', $repository->getAlternative(123));
    }
}
