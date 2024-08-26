<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\AlignmentTransformer;
use PHPUnit\Framework\TestCase;
use Municipio\PostTypeDesign\GetFields;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\UpdateOption;

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
