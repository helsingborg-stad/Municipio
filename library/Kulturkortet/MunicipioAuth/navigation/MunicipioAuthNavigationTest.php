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
