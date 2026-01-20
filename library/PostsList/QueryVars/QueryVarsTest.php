<?php

namespace Municipio\PostsList\QueryVars;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class QueryVarsTest extends TestCase
{
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
