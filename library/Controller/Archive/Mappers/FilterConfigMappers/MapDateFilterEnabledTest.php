<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use PHPUnit\Framework\TestCase;

class MapDateFilterEnabledTest extends TestCase
{
    public function testReturnsTrueWhenDateRangeEnabled()
    {
        $mapper = new MapDateFilterEnabled();
        $config = [
            'archiveProps' => (object) [
                'enabledFilters' => ['date_range', 'other']
            ]
        ];
        $this->assertTrue($mapper->map($config));
    }

    public function testReturnsFalseWhenDateRangeNotEnabled()
    {
        $mapper = new MapDateFilterEnabled();
        $config = [
            'archiveProps' => (object) [
                'enabledFilters' => ['other']
            ]
        ];
        $this->assertFalse($mapper->map($config));
    }

    public function testReturnsFalseWhenEnabledFiltersMissing()
    {
        $mapper = new MapDateFilterEnabled();
        $config = [
            'archiveProps' => (object) []
        ];
        $this->assertFalse($mapper->map($config));
    }
}
