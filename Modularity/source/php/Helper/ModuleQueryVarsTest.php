<?php

declare(strict_types=1);

namespace Modularity\Helper;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ModuleQueryVarsTest extends TestCase
{
    #[TestDox('extract() returns the query vars that belong to the module')]
    public function testExtractReturnsTheModulesOwnQueryVars(): void
    {
        $queryVars = ModuleQueryVars::fromModule($this->createModule());

        static::assertSame(
            ['mod-text-123-page' => '2', 'mod-text-123-filter' => 'news'],
            $queryVars->extract(['mod-text-123-page' => '2', 'mod-text-123-filter' => 'news']),
        );
    }

    #[TestDox('extract() ignores query vars of other modules and unknown mod-* query vars')]
    public function testExtractIgnoresQueryVarsThatDoNotBelongToTheModule(): void
    {
        $queryVars = ModuleQueryVars::fromModule($this->createModule());

        static::assertSame(
            [],
            $queryVars->extract([
                'mod-unknown' => 'random-value',
                'mod-posts-123-page' => '2',
                'mod-text-1234-page' => '2',
                'utm_source' => 'newsletter',
            ]),
        );
    }

    #[TestDox('extract() ignores query vars that do not have a string value')]
    public function testExtractIgnoresNonStringValues(): void
    {
        $queryVars = ModuleQueryVars::fromModule($this->createModule());

        static::assertSame([], $queryVars->extract(['mod-text-123-page' => ['2']]));
    }

    private function createModule(): \WP_Post
    {
        $module = new \WP_Post((object) []);
        $module->ID = 123;
        $module->post_type = 'mod-text';

        return $module;
    }
}
