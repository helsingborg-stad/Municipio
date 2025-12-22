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

        $callableProvider = new GetPaginationComponentArguments(3, 2, 'page', 'test-id');

        $this->assertEquals(
            [
                'list' => [
                    ['href' => '/posts?page=1#test-id', 'label' => '1'],
                    ['href' => '/posts?page=2#test-id', 'label' => '2'],
                    ['href' => '/posts?page=3#test-id', 'label' => '3'],
                ],
                'current' => 2,
                'linkPrefix' => 'page',
            ],
            $callableProvider->getCallable()(),
        );
    }

    #[TestDox('It should return empty array when total pages is less than minimum')]
    public function testGetPaginationComponentArgumentsWithLessThanMinimumPages(): void
    {
        $callableProvider = new GetPaginationComponentArguments(1, 1, 'page', 'test-id');

        $this->assertEquals([], $callableProvider->getCallable()());
    }
}
