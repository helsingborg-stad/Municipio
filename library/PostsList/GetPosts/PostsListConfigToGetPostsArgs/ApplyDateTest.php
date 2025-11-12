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

        $this->assertEquals('2022-01-01', $result['date_query']['after']);
        $this->assertEquals('2022-01-20', $result['date_query']['before']);
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

        $this->assertArrayNotHasKey('before', $result['date_query']);
        $this->assertEquals('2022-01-01', $result['date_query']['after']);
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

        $this->assertEquals('2022-01-20', $result['date_query']['before']);
        $this->assertArrayNotHasKey('after', $result['date_query']);
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

    #[TestDox('applies column if is post_date')]
    public function testAppliesColumnIfIsEitherPostDateOrPostModified(): void
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

            public function getDateSource(): string
            {
                return 'post_date';
            }
        };

        $applier = new ApplyDate();
        $result  = $applier->apply($config, []);

        $this->assertEquals('post_date', $result['date_query']['column']);
    }

    #[TestDox('applies column if is post_modified')]
    public function testAppliesColumnIfIsPostModified(): void
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

            public function getDateSource(): string
            {
                return 'post_modified';
            }
        };

        $applier = new ApplyDate();
        $result  = $applier->apply($config, []);

        $this->assertEquals('post_modified', $result['date_query']['column']);
    }

    #[TestDox('applies meta_query if column is not known')]
    public function testAppliesMetaQueryIfColumnIsNotKnown(): void
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

            public function getDateSource(): string
            {
                return 'custom_field';
            }
        };

        $applier = new ApplyDate();
        $result  = $applier->apply($config, []);

        $this->assertArrayNotHasKey('date_query', $result);
        $this->assertEquals([
        'meta_query' => [
            [
                'key'     => 'custom_field',
                'value'   => ['2022-01-01', '2022-01-20'],
                'compare' => 'BETWEEN',
                'type'    => 'DATE'
            ]
        ]
        ], $result);
    }

    #[TestDox('applies meta_query with only from if column is custom_field and to is not set')]
    public function testAppliesMetaQueryWithOnlyFromIfColumnIsCustomFieldAndToIsNotSet(): void
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

            public function getDateSource(): string
            {
                return 'custom_field';
            }
        };

        $applier = new ApplyDate();
        $result  = $applier->apply($config, []);

        $this->assertEquals(['meta_query' => [[
            'key'     => 'custom_field',
            'value'   => ['2022-01-01', null],
            'compare' => '>=',
            'type'    => 'DATE'
        ]]], $result);
    }

    #[TestDox('applies meta_query with only to if column is custom_field and from is not set')]
    public function testAppliesMetaQueryWithOnlyToIfColumnIsCustomFieldAndFromIsNotSet(): void
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

            public function getDateSource(): string
            {
                return 'custom_field';
            }
        };

        $applier = new ApplyDate();
        $result  = $applier->apply($config, []);

        $this->assertEquals(['meta_query' => [[
            'key'     => 'custom_field',
            'value'   => [null, '2022-01-20'],
            'compare' => '<=',
            'type'    => 'DATE'
        ]]], $result);
    }
}
