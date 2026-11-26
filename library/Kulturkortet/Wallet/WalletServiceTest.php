<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use Municipio\AppleWallet\ApplePassDataStore;
use Municipio\AppleWallet\ApplePassCardConfig;
use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/** Tests short-lived Apple Wallet pass URL creation. */
class WalletServiceTest extends TestCase
{
    /** Verifies that pass data is stored briefly without retaining unrelated ticket data. */
    public function testCreatesShortLivedUrlAndStoresOnlyRequiredTicketData(): void
    {
        $wpService = new FakeWpService(['setTransient' => true]);
        $config = $this->createMock(ApplePassCardConfig::class);
        $config->method('hasConfiguration')->willReturn(true);
        $navigation = $this->createMock(MunicipioAuthNavigationInterface::class);
        $navigation->expects(static::once())->method('getModifiedHomeUrl')
            ->with([WalletService::APPLE_PASS_EXPIRED_QUERY_VAR], static::arrayHasKey(WalletService::APPLE_PASS_QUERY_VAR))
            ->willReturnCallback(
                static fn(array $removeQueryArgs, array $addQueryArgs): string => 'https://example.test/?' . http_build_query($addQueryArgs),
            );

        $url = (new WalletService($config, new ApplePassDataStore($wpService, WalletService::APPLE_PASS_TRANSIENT_PREFIX)))
            ->createApplePassUrl([
                'id' => 'ticket-1',
                'firstname' => 'Ada',
                'lastname' => 'Lovelace',
                'validFrom' => '2030-01-01',
                'validUntil' => '2030-12-31',
                'barcode' => 'ticket-barcode',
                'civicRegistrationNumber' => 'must-not-be-stored',
            ], $navigation);

        static::assertIsString($url);
        static::assertStringContainsString(WalletService::APPLE_PASS_QUERY_VAR . '=', $url);
        $transientCalls = $wpService->methodCalls['setTransient'] ?? [];
        static::assertCount(1, $transientCalls);
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
        $token = $query[WalletService::APPLE_PASS_QUERY_VAR] ?? '';
        static::assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);
        static::assertSame(
            WalletService::APPLE_PASS_TRANSIENT_PREFIX . hash('sha256', $token),
            $transientCalls[0][0],
        );
        static::assertSame(300, $transientCalls[0][2]);
        static::assertSame('ticket-barcode', $transientCalls[0][1]['barcode']);
        static::assertArrayNotHasKey('civicRegistrationNumber', $transientCalls[0][1]);
    }

    /** Verifies that disabled Apple Wallet does not create temporary pass data. */
    public function testDoesNotCreatePassUrlWhenAppleWalletIsDisabled(): void
    {
        $wpService = new FakeWpService(['setTransient' => true]);
        $config = $this->createMock(ApplePassCardConfig::class);
        $config->method('hasConfiguration')->willReturn(false);
        $navigation = $this->createMock(MunicipioAuthNavigationInterface::class);

        $url = (new WalletService($config, new ApplePassDataStore($wpService, WalletService::APPLE_PASS_TRANSIENT_PREFIX)))
            ->createApplePassUrl(['barcode' => 'ticket-barcode'], $navigation);

        static::assertNull($url);
        static::assertArrayNotHasKey('setTransient', $wpService->methodCalls);
    }
}
