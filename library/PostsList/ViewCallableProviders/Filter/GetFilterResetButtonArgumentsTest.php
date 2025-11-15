<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\__;

class GetFilterResetButtonArgumentsTest extends TestCase
{
    #[TestDox('returns the correct reset button arguments')]
    public function testReturnsCorrectResetButtonArguments(): void
    {
        $getPostsConfig                = $this->createGetPostsConfig(false);
        $getFilterResetButtonArguments = new GetFilterResetButtonArguments(
            $getPostsConfig,
            $this->createFilterConfig(),
            $this->createWpService()
        );

        $this->assertEquals([
            'text' => 'Reset search',
            'type' => 'basic',
            'href' => 'filter-reset-url',
        ], $getFilterResetButtonArguments->getCallable()());
    }

    #[TestDox('returns the correct reset button arguments when facetting is enabled')]
    public function testReturnsCorrectResetButtonArgumentsWhenFacettingIsEnabled(): void
    {
        $getPostsConfig                = $this->createGetPostsConfig(true);
        $getFilterResetButtonArguments = new GetFilterResetButtonArguments(
            $getPostsConfig,
            $this->createFilterConfig(),
            $this->createWpService()
        );

        $this->assertEquals([
            'text' => 'Reset filter',
            'type' => 'basic',
            'href' => 'filter-reset-url',
        ], $getFilterResetButtonArguments->getCallable()());
    }

    private function createGetPostsConfig(bool $isFacettingEnabled): GetPostsConfigInterface
    {
        return new class ($isFacettingEnabled) extends DefaultGetPostsConfig {
            public function __construct(private bool $facettingEnabled)
            {
            }

            public function isFacettingTaxonomyQueryEnabled(): bool
            {
                return $this->facettingEnabled;
            }
        };
    }

    private function createFilterConfig(): FilterConfigInterface
    {
        return new class extends DefaultFilterConfig {
            public function getResetUrl(): ?string
            {
                return 'filter-reset-url';
            }
        };
    }

    private function createWpService(): __
    {
        return new class implements __ {
            public function __(string $text, string $domain = 'default'): string
            {
                return $text;
            }
        };
    }
}
