<?php

namespace Municipio\Upgrade\V41;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MigrateCustomCssTest extends TestCase
{
    #[TestDox('replaces old CSS with new ones as defined in the replacement map')]
    public function testReplacesOldCssWithNew(): void
    {
        $css = '.site-header { background-color: var(--foo-variable); }';
        $replacementMap = ['--foo-variable' => '--bar-variable'];

        $migratedCss = (new MigrateCustomCss())->migrate($css, $replacementMap);

        static::assertStringContainsString('.site-header { background-color: var(--bar-variable); }', $migratedCss);
    }

    #[TestDox('wraps CSS in a layer if it does not contain any layers')]
    public function testWrapsCssInLayerIfNoLayers(): void
    {
        $css = '.site-header { background-color: var(--foo-variable); }';

        $migratedCss = (new MigrateCustomCss())->migrate($css, []);

        static::assertStringContainsString('@layer theme {', $migratedCss);
    }

    #[TestDox('does not wrap CSS in a layer if it already contains a layer')]
    public function testDoesNotWrapCssInLayerIfAlreadyContainsLayer(): void
    {
        $css = '@layer custom { .site-header { background-color: var(--foo-variable); } }';

        $migratedCss = (new MigrateCustomCss())->migrate($css, []);

        static::assertSame($css, $migratedCss);
    }
}
