<?php

namespace Municipio\MirroredPost\Utils\IsMirroredPost;

use PHPUnit\Framework\TestCase;

class IsMirroredPostTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $isMirroredPost = new IsMirroredPost();

        $this->assertInstanceOf(IsMirroredPost::class, $isMirroredPost);
    }
}
