<?php

declare(strict_types=1);

namespace Municipio\PostsList\Config\GetPostsConfig;

use Municipio\PostsList\Config\GetPostsConfig\GetTermsFromGetParams\GetTermsFromGetParams;
use Municipio\PostsList\QueryVars\QueryVars;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetPostsConfigUsingGetParamsDecoratorTest extends TestCase
{
    #[TestDox('getSearch() returns search from GET params when available')]
    public function testPopulatesSearchFromGetParams(): void
    {
        $baseConfig = new DefaultGetPostsConfig();
        $queryVarsPrefix = 'test';
        $queryVars = new QueryVars($queryVarsPrefix);
        $getParams = [$queryVarsPrefix . 'search' => 'test search'];
        $decoratedConfig = new GetPostsConfigUsingGetParamsDecorator(
            $baseConfig,
            $getParams,
            $queryVars,
            $this->createMock(GetTermsFromGetParams::class),
        );

        static::assertSame('test search', $decoratedConfig->getSearch());
    }

    #[TestDox('getSearch() falls back to inner config when GET param is not set')]
    public function testFallsBackToInnerConfigSearch(): void
    {
        $baseConfig = new class extends DefaultGetPostsConfig {
            public function getSearch(): null|string
            {
                return 'inner search';
            }
        };
        $queryVarsPrefix = 'test';
        $queryVars = new QueryVars($queryVarsPrefix);
        $getParams = []; // No search param
        $decoratedConfig = new GetPostsConfigUsingGetParamsDecorator(
            $baseConfig,
            $getParams,
            $queryVars,
            $this->createMock(GetTermsFromGetParams::class),
        );

        static::assertSame('inner search', $decoratedConfig->getSearch());
    }

    #[TestDox('getDateFrom() populates date_from from GET params when available')]
    public function testPopulatesDateFromFromGetParams(): void
    {
        $baseConfig = new DefaultGetPostsConfig();
        $queryVarsPrefix = 'test';
        $queryVars = new QueryVars($queryVarsPrefix);
        $getParams = [$queryVarsPrefix . 'date_from' => '2023-01-01'];
        $decoratedConfig = new GetPostsConfigUsingGetParamsDecorator(
            $baseConfig,
            $getParams,
            $queryVars,
            $this->createMock(GetTermsFromGetParams::class),
        );

        static::assertSame('2023-01-01', $decoratedConfig->getDateFrom());
    }

    #[TestDox('getDateFrom() falls back to inner config when GET param is not set')]
    public function testFallsBackToInnerConfigDateFrom(): void
    {
        $baseConfig = new class extends DefaultGetPostsConfig {
            public function getDateFrom(): null|string
            {
                return '2022-12-31';
            }
        };
        $queryVarsPrefix = 'test';
        $queryVars = new QueryVars($queryVarsPrefix);
        $getParams = []; // No date_from param
        $decoratedConfig = new GetPostsConfigUsingGetParamsDecorator(
            $baseConfig,
            $getParams,
            $queryVars,
            $this->createMock(GetTermsFromGetParams::class),
        );

        static::assertSame('2022-12-31', $decoratedConfig->getDateFrom());
    }

    #[TestDox('getDateTo() populates date_to from GET params when available')]
    public function testPopulatesDateToFromGetParams(): void
    {
        $baseConfig = new DefaultGetPostsConfig();
        $queryVarsPrefix = 'test';
        $queryVars = new QueryVars($queryVarsPrefix);
        $getParams = [$queryVarsPrefix . 'date_to' => '2023-12-31'];
        $decoratedConfig = new GetPostsConfigUsingGetParamsDecorator(
            $baseConfig,
            $getParams,
            $queryVars,
            $this->createMock(GetTermsFromGetParams::class),
        );

        static::assertSame('2023-12-31', $decoratedConfig->getDateTo());
    }

    #[TestDox('getDateTo() falls back to inner config when GET param is not set')]
    public function testFallsBackToInnerConfigDateTo(): void
    {
        $baseConfig = new class extends DefaultGetPostsConfig {
            public function getDateTo(): null|string
            {
                return '2022-11-30';
            }
        };
        $queryVarsPrefix = 'test';
        $queryVars = new QueryVars($queryVarsPrefix);
        $getParams = []; // No date_to param
        $decoratedConfig = new GetPostsConfigUsingGetParamsDecorator(
            $baseConfig,
            $getParams,
            $queryVars,
            $this->createMock(GetTermsFromGetParams::class),
        );

        static::assertSame('2022-11-30', $decoratedConfig->getDateTo());
    }

    #[TestDox('getTerms() populates terms from GET params when available')]
    public function testPopulatesTermsFromGetParams(): void
    {
        $termSlug = 'activity';
        $termTaxonomy = 'category';
        $baseConfig = new DefaultGetPostsConfig();
        $queryVarsPrefix = 'test';
        $queryVars = new QueryVars($queryVarsPrefix);
        $getParams = [$queryVarsPrefix . $termTaxonomy => $termSlug];
        $getTermsMock = $this->createMock(GetTermsFromGetParams::class);
        $getTermsMock
            ->method('getTerms')
            ->willReturn([
                (object) ['term_id' => 1, 'slug' => $termSlug, 'taxonomy' => $termTaxonomy],
            ]);
        $decoratedConfig = new GetPostsConfigUsingGetParamsDecorator(
            $baseConfig,
            $getParams,
            $queryVars,
            $getTermsMock,
        );

        // Since we cannot mock WP functions here, we will just check that the method returns an array
        $terms = $decoratedConfig->getTerms();
        static::assertSame('activity', $terms[0]->slug);
        static::assertSame('category', $terms[0]->taxonomy);
    }

    #[TestDox('getTerms() appends terms from GET params to inner config terms')]
    public function testAppendsTermsFromGetParamsToInnerConfig(): void
    {
        $baseConfig = new class extends DefaultGetPostsConfig {
            public function getTerms(): array
            {
                return [
                    (object) ['term_id' => 2, 'slug' => 'news', 'taxonomy' => 'category'],
                ];
            }
        };
        $queryVarsPrefix = 'test';
        $queryVars = new QueryVars($queryVarsPrefix);
        $getParams = [$queryVarsPrefix . 'category' => 'activity'];
        $getTermsMock = $this->createMock(GetTermsFromGetParams::class);
        $getTermsMock
            ->method('getTerms')
            ->willReturn([
                (object) ['term_id' => 1, 'slug' => 'activity', 'taxonomy' => 'category'],
            ]);
        $decoratedConfig = new GetPostsConfigUsingGetParamsDecorator(
            $baseConfig,
            $getParams,
            $queryVars,
            $getTermsMock,
        );

        $terms = $decoratedConfig->getTerms();
        static::assertCount(2, $terms);
        static::assertSame('news', $terms[0]->slug);
        static::assertSame('activity', $terms[1]->slug);
    }
}
