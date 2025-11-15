<?php

namespace Municipio\PostsList\Config\GetPostsConfig\GetParameterFromGetParams;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetParameterFromGetParamsTest extends TestCase
{
    #[TestDox("extracts parameter from GET request")]
    public function testExtractsParameterFromGetRequest(): void
    {
        $parameterName             = 'prefix_s';
        $getParams[$parameterName] = 'search term';

        $this->assertSame('search term', (new GetParameterFromGetParams())->getParam($getParams, $parameterName));
    }

    #[TestDox("returns null if not found")]
    public function testReturnsNullIfNotFound(): void
    {
        $parameterName = 'prefix_s';
        $getParams     = [];

        $this->assertNull((new GetParameterFromGetParams())->getParam($getParams, $parameterName));
    }
}
