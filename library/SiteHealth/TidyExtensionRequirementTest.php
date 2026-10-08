<?php

declare(strict_types=1);

namespace Municipio\SiteHealth;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class TidyExtensionRequirementTest extends TestCase
{
    public function testAddsItsTestToTheDirectSiteHealthTests(): void
    {
        $feature = new TidyExtensionRequirement($this->createMock(AddFilter::class));

        $tests = $feature->addTest([]);

        static::assertArrayHasKey('municipio_tidy_extension', $tests['direct']);
        static::assertSame(__('Check the PHP Tidy extension', 'municipio'), $tests['direct']['municipio_tidy_extension']['label']);
    }

    public function testReportsAHealthyResultWhenTidyIsAvailable(): void
    {
        $feature = new TidyExtensionRequirement(
            $this->createMock(AddFilter::class),
            static fn(): bool => true,
        );

        $result = $feature->testTidyExtension();

        static::assertSame('good', $result['status']);
        static::assertSame('municipio_tidy_extension', $result['test']);
    }

    public function testReportsACriticalResultWhenTidyIsUnavailable(): void
    {
        $feature = new TidyExtensionRequirement(
            $this->createMock(AddFilter::class),
            static fn(): bool => false,
        );

        $result = $feature->testTidyExtension();

        static::assertSame('critical', $result['status']);
        static::assertSame('municipio_tidy_extension', $result['test']);
    }
}
