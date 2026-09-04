<?php

namespace Municipio\Api\PostsList;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(PostsListRender::class)]
class PostsListRenderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_GET    = [];
        $_SERVER = [];
    }

    #[TestDox('hydrateRequestGlobals updates REQUEST_URI and excludes internal params from GET')]
    public function testHydrateRequestGlobalsUsesArchiveRequestUri(): void
    {
        $sut = new class extends PostsListRender {
            public function exposeHydrateRequestGlobals(array $params, ?string $requestUri): void
            {
                $this->hydrateRequestGlobals($params, $requestUri);
            }
        };

        $sut->exposeHydrateRequestGlobals(
            [
                'attributes' => '{}',
                'requestUri' => '/event?archive_page=2',
                'archive_page' => '2',
                'archive_category' => 'innovation',
            ],
            '/event?archive_page=2',
        );

        $this->assertSame('/event?archive_page=2', $_SERVER['REQUEST_URI']);
        $this->assertSame('2', $_GET['archive_page']);
        $this->assertSame('innovation', $_GET['archive_category']);
        $this->assertArrayNotHasKey('attributes', $_GET);
        $this->assertArrayNotHasKey('requestUri', $_GET);
    }

    #[TestDox('hydrateRequestGlobals ignores invalid request URIs')]
    public function testHydrateRequestGlobalsIgnoresExternalRequestUri(): void
    {
        $_SERVER['REQUEST_URI'] = '/existing';

        $sut = new class extends PostsListRender {
            public function exposeHydrateRequestGlobals(array $params, ?string $requestUri): void
            {
                $this->hydrateRequestGlobals($params, $requestUri);
            }
        };

        $sut->exposeHydrateRequestGlobals(['archive_page' => '3'], 'https://example.com/not-local');

        $this->assertSame('/existing', $_SERVER['REQUEST_URI']);
        $this->assertSame('3', $_GET['archive_page']);
    }
}