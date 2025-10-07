<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\AlignmentTransformer;
use PHPUnit\Framework\TestCase;

class AlignmentTransformerTest extends TestCase
{
    public function testTransformReturnsTransformedArray()
    {
        $alignmentTransformerInstance = new AlignmentTransformer($this->getData());
        $items                        = $this->getItems();

        $result = $alignmentTransformerInstance->transform($items, 'header');

        $this->assertTrue(isset($result['modified']['alignment']['menu']));
    }

    private function getItems()
    {
        return [
            'desktop'  => [
                'menu' => 0
            ],
            'mobile'   => [],
            'modified' => [
                'menu' => [],
            ]
        ];
    }

    private function getData()
    {
        return (object) [
            'header' => (object) [
                'menu' => (object) [
                    'align' => 'alignment'
                ],
            ],
        ];
    }
}
