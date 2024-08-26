<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\MenuOrderTransformer;
use PHPUnit\Framework\TestCase;

class MenuOrderTransformerTest extends TestCase
{
    public function testTransformReturnsTransformedArray()
    {
        $menuOrderTransformerInstance = new MenuOrderTransformer('@modifier');

        $items = [
            'desktop' => [
                'item1' => 0,
                'item2' => 1
            ],
            'mobile'  => [
                'item2' => 0,
                'item1' => 1
            ],
        ];

        $result = $menuOrderTransformerInstance->transform($items);

        $this->assertEquals($result['modified']['item1'], ['u-order--1', 'u-order--0@modifier']);
        $this->assertEquals($result['modified']['item2'], ['u-order--0', 'u-order--1@modifier']);
    }

    public function testTransformReturnsItemsWithModifiedKey()
    {
        $menuOrderTransformerInstance = new MenuOrderTransformer('@modifier');

        $items = [
            'desktop' => [],
            'mobile'  => [],
        ];

        $result = $menuOrderTransformerInstance->transform($items);

        $this->assertEquals($result['modified'], []);
    }
}
