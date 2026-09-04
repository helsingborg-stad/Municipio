<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Attachment;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;

class AttachmentConfigTest extends TestCase
{
    public function testIsDisabledWithoutSelectedMimeTypes(): void
    {
        $config = new AttachmentConfig(new FakeAcfService(['getField' => false]));

        static::assertFalse($config->isEnabled());
        static::assertSame([], $config->getMimeTypes());
    }

    public function testReturnsSelectedMimeTypes(): void
    {
        $config = new AttachmentConfig(new FakeAcfService([
            'getField' => ['application/pdf', 'text/plain'],
        ]));

        static::assertTrue($config->isEnabled());
        static::assertSame(['application/pdf', 'text/plain'], $config->getMimeTypes());
    }
}