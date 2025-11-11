<?php

namespace Municipio\PostsList\ViewCallableProviders\Pagination;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetPaginationComponentArgumentsTest extends TestCase
{
    #[TestDox('It should return correct pagination arguments')]
    public function testGetPaginationComponentArguments(): void
    {
        // Set current URL function for testing
        $_SERVER['REQUEST_URI'] = 'https://example.com/posts';

        $callableProvider = new GetPaginationComponentArguments(3, 2, 'page');

        $this->assertEquals([
            'list'       => [
                [ 'href' => '/posts?page=1','label' => '1', ],
                [ 'href' => '/posts?page=2','label' => '2', ],
                [ 'href' => '/posts?page=3','label' => '3', ],
            ],
            'current'    => 2,
            'linkPrefix' => 'page'
        ], $callableProvider->getCallable()());
    }
}
