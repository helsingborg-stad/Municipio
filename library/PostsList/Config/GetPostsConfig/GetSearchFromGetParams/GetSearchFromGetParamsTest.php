<?php

namespace Municipio\PostsList\Config\GetPostsConfig\GetSearchFromGetParams;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetSearchFromGetParamsTest extends TestCase
{
    #[TestDox("extracts search from GET request")]
    public function testExtractsSearchFromGetRequest(): void
    {
        $parameterName             = 'prefix_s';
        $getParams[$parameterName] = 'search term';

        $this->assertSame('search term', (new GetSearchFromGetParams($getParams, $parameterName))->getSearch());
    }

    #[TestDox("returns empty string if not found")]
    public function testReturnsEmptyStringIfNotFound(): void
    {
        $parameterName = 'prefix_s';
        $getParams     = [];

        $this->assertSame('', (new GetSearchFromGetParams($getParams, $parameterName))->getSearch());
    }
}
