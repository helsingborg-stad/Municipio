<?php

namespace Municipio\SchemaData\Utils;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

class GetEnabledSchemaTypesTest extends TestCase
{
    private WpService $wpService;

    private function getEnabledSchemaTypes(): array
    {
        $this->wpService       = new FakeWpService(['applyFilters' => fn ($filter, $value) => $value ]);
        $getEnabledSchemaTypes = new GetEnabledSchemaTypes($this->wpService);
        return $getEnabledSchemaTypes->getEnabledSchemaTypesAndProperties();
    }

    public function testContainsPlace()
    {
        $enabledSchemaTypes = $this->getEnabledSchemaTypes();
        $this->assertArrayHasKey('Place', $enabledSchemaTypes);
    }

    public function testContainsSchool()
    {
        $enabledSchemaTypes = $this->getEnabledSchemaTypes();
        $this->assertArrayHasKey('School', $enabledSchemaTypes);
    }

    public function testPlaceProperties()
    {
        $enabledSchemaTypes = $this->getEnabledSchemaTypes();
        $this->assertContains('geo', $enabledSchemaTypes['Place'], 'geo property is missing');
        $this->assertContains('telephone', $enabledSchemaTypes['Place'], 'telephone property is missing');
        $this->assertContains('url', $enabledSchemaTypes['Place'], 'url property is missing');
    }

    public function testContainsProject()
    {
        $this->assertArrayHasKey('Project', $this->getEnabledSchemaTypes());
    }

    public function testProjectProperties()
    {
        $enabledSchemaTypes = $this->getEnabledSchemaTypes();
        $this->assertContains('@id', $enabledSchemaTypes['Project']);
        $this->assertContains('description', $enabledSchemaTypes['Project']);
        $this->assertContains('name', $enabledSchemaTypes['Project']);
        $this->assertContains('department', $enabledSchemaTypes['Project']);
        $this->assertContains('employee', $enabledSchemaTypes['Project']);
        $this->assertContains('funding', $enabledSchemaTypes['Project']);
    }

    public function testContainsSpecialAnnouncement()
    {
        $this->assertArrayHasKey('SpecialAnnouncement', $this->getEnabledSchemaTypes());
    }

    public function testSpecialAnnouncementProperties()
    {
        $enabledSchemaTypes = $this->getEnabledSchemaTypes();
        $this->assertContains('@id', $enabledSchemaTypes['SpecialAnnouncement']);
        $this->assertContains('description', $enabledSchemaTypes['SpecialAnnouncement']);
        $this->assertContains('name', $enabledSchemaTypes['SpecialAnnouncement']);
    }

    public function testFilterIsApplied()
    {
        $enabledSchemaTypes = $this->getEnabledSchemaTypes();
        $this->assertEquals('Municipio/SchemaData/EnabledSchemaTypes', $this->wpService->methodCalls['applyFilters'][0][0]);
        $this->assertEquals($enabledSchemaTypes, $this->wpService->methodCalls['applyFilters'][0][1]);
    }
}
