<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use PHPUnit\Framework\TestCase;

class MapTextSearchEnabledTest extends TestCase
{
    public function testReturnsTrueWhenTextSearchEnabled()
    {
        $mapper = new MapTextSearchEnabled();
        $config = [
            'archiveProps' => (object) [
                'enabledFilters' => ['text_search', 'other']
            ]
        ];
        $this->assertTrue($mapper->map($config));
    }

    public function testReturnsFalseWhenTextSearchNotEnabled()
    {
        $mapper = new MapTextSearchEnabled();
        $config = [
            'archiveProps' => (object) [
                'enabledFilters' => ['other']
            ]
        ];
        $this->assertFalse($mapper->map($config));
    }

    public function testReturnsFalseWhenEnabledFiltersMissing()
    {
        $mapper = new MapTextSearchEnabled();
        $config = [
            'archiveProps' => (object) []
        ];
        $this->assertFalse($mapper->map($config));
    }
}
