<?php

namespace Municipio\Controller\SingularEvent\Mappers\Occasion;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class OccasionTest extends TestCase
{
    #[TestDox('getStartDate returns the provided startDate as string')]
    public function testGetStartDate()
    {
        $occasion = new Occasion('2025-12-24 18:00', '', false, '');
        $this->assertSame('2025-12-24 18:00', $occasion->getStartDate());
    }

    #[TestDox('getEndDate returns the provided endDateDate as string')]
    public function testGetEndDate()
    {
        $occasion = new Occasion('', '2025-12-24 18:00', false, '');
        $this->assertSame('2025-12-24 18:00', $occasion->getEndDate());
    }

    #[TestDox('isCurrent returns the provided boolean value')]
    public function testIsCurrent()
    {
        $occasion = new Occasion('2025-12-24 18:00', '', true, '');
        $this->assertTrue($occasion->isCurrent());

        $occasion = new Occasion('2025-12-24 18:00', '', false, '');
        $this->assertFalse($occasion->isCurrent());
    }

    #[TestDox('getUrl returns the provided URL string')]
    public function testGetUrl()
    {
        $occasion = new Occasion('2025-12-24 18:00', '', false, 'https://example.com/event');
        $this->assertSame('https://example.com/event', $occasion->getUrl());
    }
}
