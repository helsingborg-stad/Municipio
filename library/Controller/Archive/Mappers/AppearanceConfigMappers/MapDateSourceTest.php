<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

class MapDateSourceTest extends \PHPUnit\Framework\TestCase
{
    public function testMapWithDateFieldSet()
    {
        $mapper = new MapDateSource();

        $data = [
            'archiveProps' => (object) ['dateField' => 'custom_date_field'],
        ];

        $result = $mapper->map($data);
        $this->assertEquals('custom_date_field', $result);
    }

    public function testMapWithDateFieldNotSet()
    {
        $mapper = new MapDateSource();

        $data = [
            'archiveProps' => (object) [],
        ];

        $result = $mapper->map($data);
        $this->assertEquals('post_date', $result);
    }
}
