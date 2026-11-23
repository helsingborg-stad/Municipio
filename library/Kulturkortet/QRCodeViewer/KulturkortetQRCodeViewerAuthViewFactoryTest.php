<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\QRCodeViewer;

use ComponentLibrary\Renderer\RendererInterface;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigation;
use Municipio\Kulturkortet\MunicipioAuth\user\MunicipioAuthenticatedUser;
use Municipio\Kulturkortet\Vitec\VitecServiceInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\__;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;
use WpService\Implementations\FakeWpService;

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
            (new \DateTimeImmutable('today +8 days'))->format('Y-m-d\\T23:59:00')
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
            (new \DateTimeImmutable('today'))->format('Y-m-d\\T23:59:00')
        );

        $pastResult = $calculateDaysLeft->invoke(
            $factory,
            (new \DateTimeImmutable('today -3 days'))->format('Y-m-d\\T23:59:00')
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
            'not-a-date'
        );

        static::assertNull($result);
    }

    /** Checks what the login view receives when an expired pass sends the user back. */
    #[DataProvider('loginNoticeCases')]
    public function testLoginViewReceivesNoticeOnlyForExpiredLink(string $query, ?string $expectedNotice): void
    {
        $navigation = new MunicipioAuthNavigation(new FakeWpService([]), 'https://example.test/ticket/' . $query);
        $renderer = $this->createMock(RendererInterface::class);
        $renderer->expects(static::once())->method('render')->with(
            'kulturkortet-qr-simple',
            static::callback(static fn(array $model): bool =>
                array_key_exists('walletError', $model) && $model['walletError'] === $expectedNotice
            ),
        );
        $factory = $this->createFactory($renderer);

        $factory->whenAnonymous('/login', $navigation);
    }

    /** Provides ordinary, expired, and malformed ticket page URLs. */
    public static function loginNoticeCases(): array
    {
        return [
            'expired' => [
                '?kulturkortet_apple_pass_expired=1',
                'Länken till Apple Wallet-kortet har gått ut eller har redan använts.',
            ],
            'ordinary' => ['', null],
            'malformed' => ['?kulturkortet_apple_pass_expired%5B%5D=1', null],
        ];
    }

    /** A sign-out confirmation must not display a stale notice, even when opened with the old flag. */
    public function testLogoutViewDoesNotDisplayStaleNotice(): void
    {
        $navigation = new MunicipioAuthNavigation(
            new FakeWpService([]),
            'https://example.test/ticket/?action=logout&kulturkortet_apple_pass_expired=1',
        );
        $renderer = $this->createMock(RendererInterface::class);
        $renderer->expects(static::once())->method('render')->with(
            'kulturkortet-qr-simple',
            static::callback(static fn(array $model): bool =>
                array_key_exists('walletError', $model) && $model['walletError'] === null
            ),
        );
        $factory = $this->createFactory($renderer);

        $factory->whenLogOut(new MunicipioAuthenticatedUser(null, '', 'Test User', 'Test', 'User'), $navigation, '/login');
    }

    private function createFactory(?RendererInterface $renderer = null): KulturkortetQRCodeViewerAuthViewFactory
    {
        $wpService = new class implements WpCacheGet, WpCacheSet, __ {
            public function wpCacheGet(int|string $key, string $group = '', bool $force = false, bool &$found = null): mixed
            {
                $found = false;
                return false;
            }

            public function wpCacheSet(int|string $key, mixed $data, string $group = '', int $expire = 0): bool
            {
                return true;
            }

            public function __(string $text, string $domain = 'default'): string
            {
                return $text;
            }
        };

        $vitecService = $this->createMock(VitecServiceInterface::class);

        return new KulturkortetQRCodeViewerAuthViewFactory($wpService, $vitecService, renderer: $renderer);
    }
}
