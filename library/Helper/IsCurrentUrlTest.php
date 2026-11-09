<?php

namespace Municipio\Helper;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class IsCurrentUrlTest extends TestCase
{
    private mixed $originalRequestUri;

    protected function setUp(): void
    {
        $this->originalRequestUri = $_SERVER['REQUEST_URI'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->originalRequestUri === null) {
            unset($_SERVER['REQUEST_URI']);
        } else {
            $_SERVER['REQUEST_URI'] = $this->originalRequestUri;
        }
    }

    #[TestDox('isCurrentOrAncestorUrl() returns $expected for request $requestUri and menu link $url')]
    #[DataProvider('provideCurrentOrAncestorCases')]
    public function testIsCurrentOrAncestorUrl(string $requestUri, string $url, bool $expected): void
    {
        $_SERVER['REQUEST_URI'] = $requestUri;

        $this->assertSame($expected, IsCurrentUrl::isCurrentOrAncestorUrl($url));
    }

    public static function provideCurrentOrAncestorCases(): array
    {
        return [
            'exact path'                                   => ['/om-oss/', 'https://example.com/om-oss/', true],
            'exact path without trailing slash'            => ['/om-oss', 'https://example.com/om-oss/', true],
            'child path'                                   => ['/om-oss/team/', 'https://example.com/om-oss/', true],
            'sibling path sharing prefix'                  => ['/om-oss-2/', 'https://example.com/om-oss/', false],
            'unrelated path'                               => ['/kontakt/', 'https://example.com/om-oss/', false],
            'request query string is ignored for paths'    => ['/om-oss/?page=2', 'https://example.com/om-oss/', true],
            'link query params present in request'         => ['/sok/?typ=nyheter', 'https://example.com/sok/?typ=nyheter', true],
            'extra request query params are allowed'       => ['/sok/?typ=nyheter&p=2', 'https://example.com/sok/?typ=nyheter', true],
            'link query param with different value'        => ['/sok/?typ=event', 'https://example.com/sok/?typ=nyheter', false],
            'link query param missing from request'        => ['/sok/', 'https://example.com/sok/?typ=nyheter', false],
            'child path when link has query params'        => ['/sok/under/?typ=nyheter', 'https://example.com/sok/?typ=nyheter', false],
            'all link query params must match'             => ['/sok/?typ=nyheter', 'https://example.com/sok/?typ=nyheter&ar=2026', false],
            'home url never matches'                       => ['/', 'https://example.com/', false],
            'relative url is not a valid menu link'        => ['/om-oss/', '/om-oss/', false],
        ];
    }

    #[TestDox('isCurrentOrAncestorUrl() returns false when there is no request uri')]
    public function testIsCurrentOrAncestorUrlReturnsFalseWithoutRequestUri(): void
    {
        unset($_SERVER['REQUEST_URI']);

        $this->assertFalse(IsCurrentUrl::isCurrentOrAncestorUrl('https://example.com/om-oss/'));
    }
}
