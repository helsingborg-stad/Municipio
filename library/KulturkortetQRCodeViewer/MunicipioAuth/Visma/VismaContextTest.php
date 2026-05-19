<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\Visma;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class VismaContextTest extends TestCase
{
    #[TestDox('returns query parameter value if it exists from home url')]
    public function testGetQueryParameter(): void
    {
        $context = new VismaContext(
            new VismaAuthConfig(),
            new FakeWpService(),
            homeUrl: 'http://example.com/?ts_session_id=12345',
        );
        static::assertSame('12345', $context->getQueryParameter('ts_session_id'));
        static::assertNull($context->getQueryParameter('non_existent_param'));
    }

    #[TestDox('returns home url')]
    public function testGetHomeUrl(): void
    {
        $context = new VismaContext(
            new VismaAuthConfig(),
            new FakeWpService(),
            homeUrl: 'http://example.com/?ts_session_id=12345',
        );
        static::assertSame('http://example.com/?ts_session_id=12345', $context->getHomeUrl());
    }

    #[TestDox('shouldRemoteGetApiSession returns true if ts_session_id query parameter exists')]
    public function testShouldRemoteGetApiSession(): void
    {
        $context = new VismaContext(
            new VismaAuthConfig(),
            new FakeWpService(),
            homeUrl: 'http://example.com/?ts_session_id=12345',
        );
        static::assertTrue($context->shouldRemoteGetApiSession());
    }

    #[TestDox('shouldRemoteGetApiSession returns false if ts_session_id query parameter does not exist')]
    public function testShouldRemoteGetApiSessionFalse(): void
    {
        $context = new VismaContext(
            new VismaAuthConfig(),
            new FakeWpService(),
            homeUrl: 'http://example.com/',
        );
        static::assertFalse($context->shouldRemoteGetApiSession());
    }
}
