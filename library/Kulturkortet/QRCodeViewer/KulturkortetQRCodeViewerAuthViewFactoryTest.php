<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\QRCodeViewer;

use Municipio\Kulturkortet\Vitec\VitecServiceInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;

class KulturkortetQRCodeViewerAuthViewFactoryTest extends TestCase
{
    #[TestDox('calculateDaysLeft returns remaining calendar days for a future date')]
    public function testCalculateDaysLeftReturnsFutureDays(): void
    {
        $factory = $this->createFactory();

        $calculateDaysLeft = new \ReflectionMethod($factory, 'calculateDaysLeft');
        $calculateDaysLeft->setAccessible(true);

        $result = $calculateDaysLeft->invoke(
            $factory,
            '2026-06-10T23:59:00',
            new \DateTimeImmutable('2026-06-02T12:00:00')
        );

        static::assertSame(8, $result);
    }

    #[TestDox('calculateDaysLeft returns 0 when ticket expiry date is today or in the past')]
    public function testCalculateDaysLeftReturnsZeroForTodayOrPast(): void
    {
        $factory = $this->createFactory();

        $calculateDaysLeft = new \ReflectionMethod($factory, 'calculateDaysLeft');
        $calculateDaysLeft->setAccessible(true);

        $todayResult = $calculateDaysLeft->invoke(
            $factory,
            '2026-06-02T23:59:00',
            new \DateTimeImmutable('2026-06-02T12:00:00')
        );

        $pastResult = $calculateDaysLeft->invoke(
            $factory,
            '2026-05-20T23:59:00',
            new \DateTimeImmutable('2026-06-02T12:00:00')
        );

        static::assertSame(0, $todayResult);
        static::assertSame(0, $pastResult);
    }

    #[TestDox('calculateDaysLeft returns null for invalid date input')]
    public function testCalculateDaysLeftReturnsNullForInvalidDate(): void
    {
        $factory = $this->createFactory();

        $calculateDaysLeft = new \ReflectionMethod($factory, 'calculateDaysLeft');
        $calculateDaysLeft->setAccessible(true);

        $result = $calculateDaysLeft->invoke(
            $factory,
            'not-a-date',
            new \DateTimeImmutable('2026-06-02T12:00:00')
        );

        static::assertNull($result);
    }

    private function createFactory(): KulturkortetQRCodeViewerAuthViewFactory
    {
        $wpService = new class implements WpCacheGet, WpCacheSet {
            public function wpCacheGet(int|string $key, string $group = '', bool $force = false, bool &$found = null): mixed
            {
                $found = false;
                return false;
            }

            public function wpCacheSet(int|string $key, mixed $data, string $group = '', int $expire = 0): bool
            {
                return true;
            }
        };

        $vitecService = $this->createMock(VitecServiceInterface::class);

        return new KulturkortetQRCodeViewerAuthViewFactory($wpService, $vitecService);
    }
}
