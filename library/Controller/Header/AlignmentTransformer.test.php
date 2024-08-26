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

        $this->assertTrue(isset($result['alignment']));
    }

    public function testTransformReturnsEmptyArrayIfNoItems()
    {
        $alignmentTransformerInstance = new AlignmentTransformer($this->getData());
        $items                        = [];

        $result = $alignmentTransformerInstance->transform($items, 'header');

        $this->assertEmpty($result);
    }

    private function getItems()
    {
        return [
            'menu' => [
                'class1'
            ]
        ];
    }

    private function getData()
    {
        return (object) [
            'header' => (object) [
                'menu' => (object) [
                    'setting' => 'alignment'
                ],
            ],
        ];
    }
}
