<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\__;

class GetFilterSubmitButtonArgumentsTest extends TestCase
{
    #[TestDox('returns the correct button arguments')]
    public function testReturnsCorrectButtonArguments(): void
    {
        $getPostsConfig                 = $this->createGetPostsConfig(false);
        $getFilterSubmitButtonArguments = new GetFilterSubmitButtonArguments($getPostsConfig, $this->createWpService());
        $callable                       = $getFilterSubmitButtonArguments->getCallable();

        $this->assertEquals([
            'text' => 'Search',
            'type' => 'submit',
            'icon' => 'search',
        ], $callable());
    }

    #[TestDox('returns the correct submit arguments when facetting is enabled')]
    public function testReturnsCorrectSubmitArgumentsWhenFacettingIsEnabled(): void
    {
        $getPostsConfig                 = $this->createGetPostsConfig(true);
        $getFilterSubmitButtonArguments = new GetFilterSubmitButtonArguments($getPostsConfig, $this->createWpService());
        $callable                       = $getFilterSubmitButtonArguments->getCallable();

        $this->assertEquals([
            'text' => 'Filter',
            'type' => 'submit',
            'icon' => 'filter_list',
        ], $callable());
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
