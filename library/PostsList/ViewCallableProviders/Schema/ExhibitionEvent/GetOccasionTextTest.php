<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders\Schema\ExhibitionEvent;

use DateTime;
use Municipio\PostObject\NullPostObject;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\_x;
use WpService\Contracts\DateI18n;

class GetOccasionTextTest extends TestCase
{
    #[TestDox('It returns null when startDate is null')]
    public function testReturnsNullWhenStartDateIsNull(): void
    {
        $schema = Schema::exhibitionEvent()->setProperty('startDate', null)->setProperty('endDate', null);
        $post = new class($schema) extends NullPostObject {
            public function __construct(
                private BaseType $schema,
            ) {}

            public function getSchema(): BaseType
            {
                return $this->schema;
            }
        };

        $provider = new GetOccasionText($this->getWpService());
        $callable = $provider->getCallable();

        static::assertNull($callable($post));
    }

    #[TestDox('It returns correct occasion text when startDate and endDate are provided')]
    public function testReturnsCorrectOccasionTextWhenStartAndEndDateProvided(): void
    {
        $schema = Schema::exhibitionEvent()
            ->setProperty('startDate', '2024-01-15')
            ->setProperty('endDate', '2024-02-15');
        $post = new class($schema) extends NullPostObject {
            public function __construct(
                private BaseType $schema,
            ) {}

            public function getSchema(): BaseType
            {
                return $this->schema;
            }
        };

        $provider = new GetOccasionText($this->getWpService());
        $callable = $provider->getCallable();

        static::assertSame('15 Jan - 15 Feb 2024', $callable($post));
    }

    #[TestDox('It returns correct occasion text when only startDate is provided')]
    public function testReturnsCorrectOccasionTextWhenOnlyStartDateProvided(): void
    {
        $schema = Schema::exhibitionEvent()->setProperty('startDate', '2024-03-10')->setProperty('endDate', null);
        $post = new class($schema) extends NullPostObject {
            public function __construct(
                private BaseType $schema,
            ) {}

            public function getSchema(): BaseType
            {
                return $this->schema;
            }
        };
        $provider = new GetOccasionText($this->getWpService());
        $callable = $provider->getCallable();
        static::assertSame('10 Mar - until further notice', $callable($post));
    }

    #[TestDox('It handles schema data being DateTime objects')]
    public function testHandlesSchemaDataBeingDateTimeObjects(): void
    {
        $schema = Schema::exhibitionEvent()
            ->setProperty('startDate', new DateTime('2024-04-05'))
            ->setProperty('endDate', new DateTime('2024-05-05'));
        $post = new class($schema) extends NullPostObject {
            public function __construct(
                private BaseType $schema,
            ) {}

            public function getSchema(): BaseType
            {
                return $this->schema;
            }
        };

        $provider = new GetOccasionText($this->getWpService());
        $callable = $provider->getCallable();

        static::assertSame('5 Apr - 5 May 2024', $callable($post));
    }

    private function getWpService(): DateI18n&_x
    {
        return new class implements DateI18n, _x {
            public function dateI18n($format, $timestamp = null, $gmt = false): string
            {
                return date($format, $timestamp ?? time());
            }

            public function _x($text, $context, $domain = 'default'): string
            {
                return $text;
            }
        };
    }
}
