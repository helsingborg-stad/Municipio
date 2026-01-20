<?php

namespace Municipio\PostsList\QueryVars\QueryVarRegistrar;

use Municipio\PostsList\QueryVars\QueryVars;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class QueryVarRegistrarTest extends TestCase
{
    #[TestDox("Registers query vars")]
    public function testRegisterQueryVar(): void
    {
        $prefix    = rand(1000, 9999); // Random prefix to avoid conflicts with other tests
        $queryVars = new QueryVars($prefix);
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

        $instance = new QueryVarRegistrar($queryVars, $wpService);
        $instance->register();
        $registeredQueryVars = $wpService->addFilterCalls[0]['callback']([]);

        $this->assertCount(1, $wpService->addFilterCalls);
        $this->assertSame('query_vars', $wpService->addFilterCalls[0]['hookName']); // Verify the hook name
        $this->assertContains($prefix . 'page', $registeredQueryVars);
        $this->assertContains($prefix . 'date_from', $registeredQueryVars);
        $this->assertContains($prefix . 'date_to', $registeredQueryVars);
        $this->assertContains($prefix . 'search', $registeredQueryVars);
    }
}
