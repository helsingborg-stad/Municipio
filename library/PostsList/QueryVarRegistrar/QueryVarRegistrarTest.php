<?php

namespace Municipio\PostsList\QueryVarRegistrar;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class QueryVarRegistrarTest extends TestCase
{
    #[TestDox("Register query var using `query_vars` filter")]
    public function testRegisterQueryVar(): void
    {
        $queryVar  = 'my_custom_query_var';
        $wpService = new class implements AddFilter {
            public array $addFilterCalls = [];
            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addFilterCalls[] = [
                    'hookName'     => $hookName,
                    'callback'     => $callback,
                    'priority'     => $priority,
                    'acceptedArgs' => $acceptedArgs,
                ];

                return true;
            }
        };

        $instance = new QueryVarRegistrar($wpService);
        $instance->register($queryVar);

        $this->assertCount(1, $wpService->addFilterCalls);
        $this->assertSame('query_vars', $wpService->addFilterCalls[0]['hookName']);
        $this->assertEquals([$queryVar], $wpService->addFilterCalls[0]['callback']([]));
    }
}
