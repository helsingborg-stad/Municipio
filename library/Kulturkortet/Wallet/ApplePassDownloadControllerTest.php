<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\Wallet;

use Municipio\Kulturkortet\MunicipioAuth\navigation\MunicipioAuthNavigationInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/** Tests navigation for unavailable Apple Wallet passes. */
class ApplePassDownloadControllerTest extends TestCase
{
    /** Expired or consumed links redirect to the ticket page without generating another pass. */
    public function testUnavailablePassRedirectsToTicketPageWithNotice(): void
    {
        $token = str_repeat('a', 64);
        $ticketUrl = 'https://example.test/my-ticket/?lang=sv&kulturkortet_apple_pass_expired=1';
        $wpService = new FakeWpService([
            'getQueryVar' => $token,
            'nocacheHeaders' => null,
            'wpSafeRedirect' => false,
        ]);
        $navigation = $this->createMock(MunicipioAuthNavigationInterface::class);
        $navigation->expects(static::once())->method('getModifiedHomeUrl')
            ->with([WalletService::APPLE_PASS_QUERY_VAR], [WalletService::APPLE_PASS_EXPIRED_QUERY_VAR => '1'])
            ->willReturn($ticketUrl);
        $store = $this->createMock(ApplePassDataStore::class);
        $store->expects(static::once())->method('consume')->with($token)->willReturn(null);
        $generator = $this->createMock(ApplePassGenerator::class);
        $generator->expects(static::never())->method('generate');

        (new ApplePassDownloadController($wpService, $store, $generator))->handle($navigation);

        static::assertCount(1, $wpService->methodCalls['nocacheHeaders']);
        static::assertSame($ticketUrl, $wpService->methodCalls['wpSafeRedirect'][0][0]);
    }

    /** The ticket page request after a redirect does not attempt another pass download. */
    public function testRequestWithoutPassTokenDoesNotRedirect(): void
    {
        $wpService = new FakeWpService(['getQueryVar' => '']);
        $navigation = $this->createMock(MunicipioAuthNavigationInterface::class);
        $store = $this->createMock(ApplePassDataStore::class);
        $store->expects(static::never())->method('consume');
        $generator = $this->createMock(ApplePassGenerator::class);
        $generator->expects(static::never())->method('generate');

        (new ApplePassDownloadController($wpService, $store, $generator))->handle($navigation);

        static::assertArrayNotHasKey('wpSafeRedirect', $wpService->methodCalls);
    }
}
