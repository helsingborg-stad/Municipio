<?php

declare(strict_types=1);

namespace Municipio\ImageConvert;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class LqipBlurTest extends TestCase
{
    public function testIdentifiesProportionalLqipOutputFilenames(): void
    {
        $blur = new LqipBlur($this->createStub(AddFilter::class));

        static::assertTrue($this->isLqipFilename($blur, '/uploads/image-15x8.webp'));
        static::assertTrue($this->isLqipFilename($blur, '/uploads/image-15x8.tmp-abc123.webp'));
        static::assertFalse($this->isLqipFilename($blur, '/uploads/image-100x56.webp'));
    }

    public function testLeavesNonLqipImagesUntouched(): void
    {
        $blur = new LqipBlur($this->createStub(AddFilter::class));

        static::assertSame('image', $blur->blur('image', null, '/uploads/image-100x56.webp'));
    }

    private function isLqipFilename(LqipBlur $blur, string $filename): bool
    {
        $method = new \ReflectionMethod(LqipBlur::class, 'isLqipFilename');

        return $method->invoke($blur, $filename);
    }
}
