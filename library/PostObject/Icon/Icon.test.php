<?php

namespace Municipio\PostObject\Icon;

use PHPUnit\Framework\TestCase;

class IconTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $icon = new Icon();
        $this->assertInstanceOf(Icon::class, $icon);
    }
}
