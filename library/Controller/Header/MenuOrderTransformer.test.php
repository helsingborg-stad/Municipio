<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\MenuOrderTransformer;
use PHPUnit\Framework\TestCase;
use Municipio\PostTypeDesign\GetFields;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\UpdateOption;

class MenuOrderTransformerTest extends TestCase
{
    public function testTransformReturnsTransformedArray()
    {
        $menuOrderTransformerInstance = new MenuOrderTransformer('@modifier');

        $desktopItems = ['desktopItem1', 'desktopItem2'];
        $mobileItems  = ['desktopItem2', 'desktopItem1'];

        $result = $menuOrderTransformerInstance->transform($desktopItems, $mobileItems);

        $this->assertEquals($result['desktopItem1'], ['u-order--1', 'u-order--0@modifier']);
        $this->assertEquals($result['desktopItem2'], ['u-order--0', 'u-order--1@modifier']);
    }

    public function testTransformReturnsEmptyArrayIfNoDesktopItems()
    {
        $menuOrderTransformerInstance = new MenuOrderTransformer('@modifier');

        $desktopItems = [];
        $mobileItems  = ['desktopItem2', 'desktopItem1'];

        $result = $menuOrderTransformerInstance->transform($desktopItems, $mobileItems);

        $this->assertEquals($result, []);
    }

    public function testTransformReturnsTransformedDesktopItems()
    {
        $menuOrderTransformerInstance = new MenuOrderTransformer('@modifier');

        $desktopItems = ['desktopItem1', 'desktopItem2'];
        $mobileItems  = [];

        $result = $menuOrderTransformerInstance->transform($desktopItems, $mobileItems);

        $this->assertEquals($result['desktopItem1'], ['u-order--0']);
        $this->assertEquals(count($result['desktopItem1']), 1);
        $this->assertEquals($result['desktopItem2'], ['u-order--1']);
        $this->assertEquals(count($result['desktopItem2']), 1);
    }
}
