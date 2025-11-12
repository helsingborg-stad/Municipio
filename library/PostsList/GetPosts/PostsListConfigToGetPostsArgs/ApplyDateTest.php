<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ApplyDateTest extends TestCase
{
    #[TestDox('applies date from posts list config to get posts args')]
    public function testAppliesDate(): void
    {

        $config = new class extends DefaultGetPostsConfig {
            public function getDateFrom(): ?string
            {
                return '2022-01-01';
            }

            public function getDateTo(): ?string
            {
                return '2022-01-20';
            }
        };

        $applier = new ApplyDate();
        $result  = $applier->apply($config, []);

        $this->assertEquals([
            'date_query' => [
                'after'  => '2022-01-01',
                'before' => '2022-01-20',
            ]
        ], $result);
    }

    #[TestDox('applies only from if set')]
    public function testAppliesOnlyFromIfSet(): void
    {
        $config = new class extends DefaultGetPostsConfig {
            public function getDateFrom(): ?string
            {
                return '2022-01-01';
            }

            public function getDateTo(): ?string
            {
                return null;
            }
        };

        $applier = new ApplyDate();
        $result  = $applier->apply($config, []);

        $this->assertEquals([
            'date_query' => [
                'after' => '2022-01-01',
            ]
        ], $result);
    }

    #[TestDox('applies only to if set')]
    public function testAppliesOnlyToIfSet(): void
    {
        $config = new class extends DefaultGetPostsConfig {
            public function getDateFrom(): ?string
            {
                return null;
            }

            public function getDateTo(): ?string
            {
                return '2022-01-20';
            }
        };

        $applier = new ApplyDate();
        $result  = $applier->apply($config, []);

        $this->assertEquals([
            'date_query' => [
                'before' => '2022-01-20',
            ]
        ], $result);
    }

    #[TestDox('applies nothing if not set')]
    public function testAppliesNothingIfNotSet(): void
    {
        $config = new class extends DefaultGetPostsConfig {
            public function getDateFrom(): ?string
            {
                return null;
            }

            public function getDateTo(): ?string
            {
                return null;
            }
        };

        $applier = new ApplyDate();
        $result  = $applier->apply($config, []);

        $this->assertEquals([], $result);
    }
}
