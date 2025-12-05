<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use Municipio\PostsList\Config\AppearanceConfig\PostDesign;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapDesignTest extends TestCase
{
    #[TestDox('It maps design from archive props correctly')]
    #[DataProvider('designDataProvider')]
    public function testMapDesignReturnsExpectedValue($input, $expected): void
    {
        $mapper = new MapDesign();
        $data   = [ 'archiveProps' => (object) ['style' => $input] ];

        $result = $mapper->map($data);
        $this->assertEquals($expected, $result);
    }

    public static function designDataProvider(): array
    {
        return [
            'cards'         => ['cards', PostDesign::CARD],
            'collection'    => ['collection', PostDesign::COLLECTION],
            'compressed'    => ['compressed', PostDesign::COMPRESSED],
            'grid'          => ['grid', PostDesign::BLOCK],
            'list'          => ['list', PostDesign::TABLE],
            'newsitem'      => ['newsitem', PostDesign::NEWSITEM],
            'schema'        => ['schema', PostDesign::SCHEMA],
            'unknown-style' => ['unknown-style', PostDesign::CARD],
        ];
    }
}
