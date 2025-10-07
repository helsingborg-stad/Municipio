<?php

namespace Municipio\Controller\SingularEvent\EnsureVisitingSingularOccasion;

use DateTime;
use Municipio\Controller\SingularEvent\EnsureVisitingSingularOccasion\Redirect\RedirectInterface;
use Municipio\Controller\SingularEvent\Mappers\Occasion\OccasionInterface;
use PHPUnit\Framework\TestCase;

class EnsureVisitingSingularOccasionTest extends TestCase
{
    /**
     * @testdox redirects to the first future occasion if no current date is set and future occasions exist
     */
    public function testRedirectsToFirstFutureOccasion(): void
    {
        $occasion1 = $this->getOccasion((new DateTime('-2 days'))->format('Y-m-d H:i:s'), 'http://example.com/past-occasion');
        $occasion2 = $this->getOccasion((new DateTime('+2 days'))->format('Y-m-d H:i:s'), 'http://example.com/future-occasion');

        $redirector = new class implements RedirectInterface {
            public function redirect(string $url): void
            {
                echo "redirected to: $url";
            }
        };

        $ensureVisiting = new EnsureVisitingSingularOccasion($redirector, null, ...[$occasion1, $occasion2]);
        $ensureVisiting->ensureVisitingSingularOccasion();

        $this->expectOutputString('redirected to: http://example.com/future-occasion');
    }

    /**
     * @testdox redirects to the last past occasion if no current date is set and no future occasions exist
     */
    public function testRedirectsToLastPastOccasion(): void
    {
        $occasion1 = $this->getOccasion((new DateTime('-2 days'))->format('Y-m-d H:i:s'), 'http://example.com/past-occasion');
        $occasion2 = $this->getOccasion((new DateTime('-1 days'))->format('Y-m-d H:i:s'), 'http://example.com/last-past-occasion');

        $redirector = new class implements RedirectInterface {
            public function redirect(string $url): void
            {
                echo "redirected to: $url";
            }
        };

        $ensureVisiting = new EnsureVisitingSingularOccasion($redirector, null, ...[$occasion1, $occasion2]);
        $ensureVisiting->ensureVisitingSingularOccasion();

        $this->expectOutputString('redirected to: http://example.com/last-past-occasion');
    }

    private function getOccasion(string $dateTime, string $url)
    {
        return new class ($dateTime, $url) implements OccasionInterface {
            public function __construct(private string $dateTime, private string $url)
            {
            }
            public function getDateTime(): string
            {
                return $this->dateTime;
            }
            public function getUrl(): string
            {
                return $this->url;
            }
            public function isCurrent(): bool
            {
                return false;
            }
        };
    }
}
