<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\IsResponsiveMenuTransformer;
use PHPUnit\Framework\TestCase;

class IsResponsiveMenuTransformerTest extends TestCase
{
    public function testResponsiveMenuTransformerSetsMobileItemsToSameAsDesktop()
    {
        $menuOrderTransformerInstance = new IsResponsiveMenuTransformer();

        $items = [
            'desktop' => [
                'item1' => 0,
                'item2' => 1
            ],
            'mobile'  => [],
        ];

        $result = $menuOrderTransformerInstance->transform($items, false);

        $this->assertEquals($result['mobile'], $result['desktop']);
    }

    public function testResponsiveMenuTransformerDoesNotSetMobileItems()
    {
        $menuOrderTransformerInstance = new IsResponsiveMenuTransformer();

        $items = [
            'desktop' => [
                'item1' => 0,
                'item2' => 1
            ],
            'mobile'  => [],
        ];

        $result = $menuOrderTransformerInstance->transform($items, true);

        $this->assertEquals($items['desktop'], $result['desktop']);
        $this->assertEquals($items['mobile'], $result['mobile']);
    }
}
