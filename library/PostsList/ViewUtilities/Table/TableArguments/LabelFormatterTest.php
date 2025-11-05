<?php

namespace Municipio\PostsList\ViewUtilities\Table\TableArguments;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\WpDate;
use WpService\Contracts\GetOption;

class LabelFormatterTest extends TestCase
{
    #[TestDox('formatTermName converts date-like term names to formatted dates')]
    public function testFormatTermName()
    {
        $labelFormatter = new LabelFormatter($this->getWpService());
        $this->assertEquals('2020-01-01', $labelFormatter->formatTermName('2020-01-01'));
        $this->assertEquals('2020-01-01', $labelFormatter->formatTermName('01/01/2020'));
        $this->assertEquals('2020-01-01', $labelFormatter->formatTermName('01-01-2020'));
        $this->assertEquals('2020-01-01', $labelFormatter->formatTermName('January 2020'));
        $this->assertEquals('2025-01-30', $labelFormatter->formatTermName('30 January, 2025'));
        $this->assertEquals('2020-01-01', $labelFormatter->formatTermName('2020-01-01T00:00:00+00:00'));
    }

    #[TestDox('formatTermName returns non-date-like term names unchanged')]
    public function testFormatTermNameReturnsUnchanged()
    {
        $labelFormatter = new LabelFormatter($this->getWpService());
        $this->assertEquals('Non-date term', $labelFormatter->formatTermName('Non-date term'));
    }

    private function getWpService(): WpDate&GetOption
    {
        return new class implements WpDate, GetOption {
            public function wpDate(string $format, int $timestamp = null, \DateTimeZone $timezone = null): string|false
            {
                $testFormat = 'Y-m-d'; // Simplified for testing
                return date($format, $timestamp);
            }

            public function getOption(string $option, mixed $defaultValue = false): mixed
            {
                if ($option === 'date_format') {
                    return 'Y-m-d';
                }

                return $defaultValue;
            }
        };
    }
}
