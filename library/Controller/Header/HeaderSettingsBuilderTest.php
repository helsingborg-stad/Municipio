<?php

namespace Municipio\Controller\Header;

use PHPUnit\Framework\TestCase;

class HeaderSettingsBuilderTest extends TestCase
{
    public function testBuildAppliesStickyAndInnerMegaMenuSettings(): void
    {
        $builder = new HeaderSettingsBuilder(
            (object) ['headerSticky' => true],
            new HeaderVisibilityClasses()
        );

        [$upperHeader, $lowerHeader] = $builder->build(
            [
                'desktop' => ['mega-menu' => ['u-display--flex']],
                'mobile' => ['mega-menu' => ['u-display--flex']],
                'modified' => ['center' => ['u-display--flex']],
            ],
            [
                'desktop' => [],
                'mobile' => [],
                'modified' => [],
            ],
            ['style' => '--municipio-header-logo-overlap-multiplier: 0.5;']
        );

        $this->assertTrue($upperHeader['sticky']);
        $this->assertFalse($lowerHeader['sticky']);
        $this->assertTrue($upperHeader['innerMegaMenu']);
        $this->assertFalse($lowerHeader['innerMegaMenu']);
        $this->assertContains('c-header--flexible-has-centered-content', $upperHeader['classList']);
        $this->assertSame(['style' => '--municipio-header-logo-overlap-multiplier: 0.5;'], $upperHeader['attributeList']);
    }

    public function testBuildUsesDefaultHeaderSettingsWhenStickyIsDisabled(): void
    {
        $builder = new HeaderSettingsBuilder(
            (object) [],
            new HeaderVisibilityClasses()
        );

        [$upperHeader, $lowerHeader] = $builder->build(
            [
                'desktop' => [],
                'mobile' => [],
                'modified' => [],
            ],
            [
                'desktop' => [],
                'mobile' => [],
                'modified' => [],
            ]
        );

        $this->assertFalse($upperHeader['sticky']);
        $this->assertFalse($lowerHeader['sticky']);
        $this->assertSame([], $upperHeader['attributeList']);
        $this->assertSame([], $lowerHeader['attributeList']);
    }
}