<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\FlipKeyValueTransformer;
use PHPUnit\Framework\TestCase;

class FlipKeyValueTransformerTest extends TestCase
{
    public function testFlipKeyValueTransformerReturnsFlippedArrays()
    {
        $flipKeyValueTransformerInstance = new FlipKeyValueTransformer();

        $array = ['key' => 'value'];

        $result = $flipKeyValueTransformerInstance->transform($array, $array);

        $this->assertEquals($result['desktop']['value'], 'key');
        $this->assertEquals($result['mobile']['value'], 'key');
    }
}
