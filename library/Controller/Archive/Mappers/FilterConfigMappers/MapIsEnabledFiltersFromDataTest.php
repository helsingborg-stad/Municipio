<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use PHPUnit\Framework\TestCase;

class MapIsEnabledFiltersFromDataTest extends TestCase
{
    public function testReturnsTrueWhenEnabledFiltersPresent()
    {
        $wpService = $this->createMock(\WpService\Contracts\ApplyFilters::class);
        $wpService->method('applyFilters')->willReturn(['filter1']);
        $mapper = new MapIsEnabledFiltersFromData($wpService);
        $config = [
            'archiveProps' => (object) [
                'enabledFilters' => ['filter1']
            ]
        ];
        $this->assertTrue($mapper->map($config));
    }

    public function testReturnsFalseWhenNoEnabledFilters()
    {
        $wpService = $this->createMock(\WpService\Contracts\ApplyFilters::class);
        $wpService->method('applyFilters')->willReturn([]);
        $mapper = new MapIsEnabledFiltersFromData($wpService);
        $config = [
            'archiveProps' => (object) [
                'enabledFilters' => []
            ]
        ];
        $this->assertFalse($mapper->map($config));
    }
}
