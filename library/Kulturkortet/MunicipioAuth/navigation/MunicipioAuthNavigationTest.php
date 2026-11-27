<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet\MunicipioAuth\navigation;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddQueryArg;
use WpService\Contracts\HomeUrl;
use WpService\Contracts\RemoveQueryArg;
use WpService\Implementations\FakeWpService;

class MunicipioAuthNavigationTest extends TestCase
{
    #[TestDox('should return query parameter value')]
    public function testGetQueryParameter(): void
    {
        $wpService = new FakeWpService([]);

        $navigation = new MunicipioAuthNavigation($wpService, 'https://www.example.com/home/?foo=bar&action=logout');

        static::assertSame('bar', $navigation->getQueryParameter('foo'));
        static::assertSame('logout', $navigation->getQueryParameter('action'));
        static::assertNull($navigation->getQueryParameter('nonexistent'));
    }

    /** Request-only flags remain readable but do not reach sign-out links or sign-in callbacks. */
    public function testRequestOnlyQueryArgsAreNotPropagatedToAuthUrls(): void
    {
        $requestUrl = 'https://example.test/ticket/?lang=sv&notice=1&ts_session_id=session';
        $cleanUrl = 'https://example.test/ticket/?lang=sv&ts_session_id=session';
        $wpService = new FakeWpService([
            'removeQueryArg' => static fn(string|array $keys, string $url): string => match ([$keys, $url]) {
                [['notice'], $requestUrl] => $cleanUrl,
                ['ts_session_id', $cleanUrl] => 'https://example.test/ticket/?lang=sv',
            },
            'addQueryArg' => static fn(array $args, string $url): string => $url . '&' . http_build_query($args),
        ]);
        $navigation = new MunicipioAuthNavigation($wpService, $requestUrl, requestOnlyQueryArgs: ['notice']);

        $logoutUrl = $navigation->getModifiedHomeUrl(addQueryArgs: ['action' => 'logout']);
        $loginCallbackUrl = $navigation->getModifiedHomeUrl(removeQueryArgs: ['ts_session_id']);

        static::assertSame('1', $navigation->getQueryParameter('notice'));
        static::assertSame($cleanUrl . '&action=logout', $logoutUrl);
        static::assertSame('https://example.test/ticket/?lang=sv', $loginCallbackUrl);
    }

    #[TestDox('should remove and add query parameters correctly')]
    public function testGetModifiedHomeUrl(): void
    {
        // $wpService = new FakeWpService([]);

        $wpService = $this->createMockForIntersectionOfInterfaces([AddQueryArg::class, RemoveQueryArg::class, HomeUrl::class]);
        $wpService->expects(static::exactly(1))->method('removeQueryArg')->with('foo', 'https://www.example.com/home/?foo=bar&action=logout')->willReturn('https://www.example.com/home/?foo=bar&action=logout');
        $wpService->expects(static::exactly(1))->method('addQueryArg')->with(['new' => 'value'], 'https://www.example.com/home/?foo=bar&action=logout')->willReturn('https://www.example.com/home/?action=logout&new=value');
        $navigation = new MunicipioAuthNavigation($wpService, 'https://www.example.com/home/?foo=bar&action=logout');
        $modifiedUrl = $navigation->getModifiedHomeUrl(['foo'], ['new' => 'value']);
        static::assertSame('https://www.example.com/home/?action=logout&new=value', $modifiedUrl);
    }
}
