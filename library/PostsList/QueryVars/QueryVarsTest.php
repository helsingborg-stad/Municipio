<?php

namespace Municipio\PostsList\QueryVars;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class QueryVarsTest extends TestCase
{
    #[TestDox('prefix may not be reused between instances')]
    public function testPrefixMayNotBeReusedBetweenInstances(): void
    {
        $queryVars = new QueryVars('prefix1_');

        try {
            $queryVars2 = new QueryVars('prefix1_');
        } catch (\Exception $e) {
            $this->assertEquals('Prefix may not be reused between instances', $e->getMessage());
            return;
        }

        $this->assertTrue(false, 'Expected exception was not thrown');
    }

    #[TestDox('parameter names is prefixed with provided prefix')]
    public function testParameterNamesIsPrefixedWithProvidedPrefix(): void
    {
        $queryVars = new QueryVars('custom_prefix_');
        $this->assertEquals('custom_prefix_page', $queryVars->getPaginationParameterName());
        $this->assertEquals('custom_prefix_date_from', $queryVars->getDateFromParameterName());
        $this->assertEquals('custom_prefix_date_to', $queryVars->getDateToParameterName());
        $this->assertEquals('custom_prefix_search', $queryVars->getSearchParameterName());
    }
}
