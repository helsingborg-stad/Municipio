<?php

namespace Municipio\Controller\Header;


use PHPUnit\Framework\TestCase;

class MenuVisibilityTransformerTest extends TestCase
{
    public function testTransformTransformDoesNothingIfShowOnBothMobileAndDesktop()
    {
        $menuVisibilityTransformerInstance = new MenuVisibilityTransformer();

        $array = [
            "desktop"  => [
                "menu" => 0,
            ],
            "mobile"   => [
                "menu" => 0,
            ],
            "modified" => [
                "menu" => [],
            ],
        ];

        $result = $menuVisibilityTransformerInstance->transform($array);

        $this->assertSame(['u-display--flex'], $result['modified']['menu']);
    }

    public function testTransformTransformAddsMobileHiddenClasses()
    {
        $menuVisibilityTransformerInstance = new MenuVisibilityTransformer();

        $array = [
            "desktop"  => [
                "menu" => 0,
            ],
            "mobile"   => [],
            "modified" => [
                "menu" => [],
            ],
        ];

        $result = $menuVisibilityTransformerInstance->transform($array);

        $this->assertEquals(
            $result['modified']['menu'],
            ['u-display--none', 'u-display--flex@lg', 'u-display--flex@xl']
        );
    }

    public function testTransformTransformAddsDesktopHiddenClasses()
    {
        $menuVisibilityTransformerInstance = new MenuVisibilityTransformer();

        $array = [
            "desktop"  => [],
            "mobile"   => [
                "menu" => 0,
            ],
            "modified" => [
                "menu" => [],
            ],
        ];

        $result = $menuVisibilityTransformerInstance->transform($array);

        $this->assertEquals($result['modified']['menu'], ['u-display--flex', 'u-display--none@lg', 'u-display--none@xl']);
    }
}
