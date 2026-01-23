<?php

declare(strict_types=1);

namespace Municipio\PostsList\QueryVars;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class QueryVarsTest extends TestCase
{
    #[TestDox('parameter names is prefixed with provided prefix')]
    public function testParameterNamesIsPrefixedWithProvidedPrefix(): void
    {
        $queryVars = new QueryVars('custom_prefix_');
        static::assertSame('custom_prefix_page', $queryVars->getPaginationParameterName());
        static::assertSame('custom_prefix_date_from', $queryVars->getDateFromParameterName());
        static::assertSame('custom_prefix_date_to', $queryVars->getDateToParameterName());
        static::assertSame('custom_prefix_search', $queryVars->getSearchParameterName());
    }

    #[TestDox('taxonomy parameter names are returned correctly')]
    public function testTaxonomyParameterNamesAreReturnedCorrectly(): void
    {
        $taxonomies = ['category'];
        $queryVars = new QueryVars('custom_prefix_', $taxonomies);
        static::assertSame(['custom_prefix_category'], $queryVars->getTaxonomyParameterNames());
    }
}
