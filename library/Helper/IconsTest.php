<?php

declare(strict_types=1);

namespace Municipio\Theme {
    function __(string $text, string $domain = 'default'): string
    {
        return $text;
    }
}

namespace Municipio\Helper {

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class IconsTest extends TestCase
{
    #[TestDox('lists every explicitly translated icon with its label')]
    public function testGetIconsIncludesLabels(): void
    {
        $icons = Icons::getIcons();
        $iconsByName = array_column($icons, 'label', 'name');

        static::assertCount(4302, $icons);
        static::assertSame('Magnifying glass', $iconsByName['search']);
        static::assertSame('House', $iconsByName['home']);
    }
}
}
