<?php

namespace Municipio\PostsList\ViewUtilities\Table\TableArguments;

use DateTimeZone;
use Municipio\PostObject\NullPostObject;
use PHPUnit\Framework\TestCase;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use WP_Error;
use WpService\Contracts\WpDate;
use WpService\Contracts\GetOption;
use WpService\Contracts\GetTerms;

class FakePost extends NullPostObject
{
    public function __construct(
        private int $id,
        private string $title,
        private string $permalink,
        private int $publishedTime,
        private array $props = []
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getPermalink(): string
    {
        return $this->permalink;
    }
    public function getPublishedTime(bool $gmt = false): int
    {
        return $this->publishedTime;
    }
    public function __get(string $key): mixed
    {
        return $this->props[$key] ?? null;
    }
}

class FakeAppearanceConfig extends DefaultAppearanceConfig
{
    public function __construct(
        private array $propsToDisplay = [],
        private array $taxonomiesToDisplay = []
    ) {
    }

    public function getPostPropertiesToDisplay(): array
    {
        return $this->propsToDisplay;
    }
    public function getTaxonomiesToDisplay(): array
    {
        return $this->taxonomiesToDisplay;
    }
}

class FakeWpService implements WpDate, GetOption, GetTerms
{
    public function wpDate(string $format, ?int $timestamp = null, ?DateTimeZone $timezone = null): string|false
    {
        return date($format, $timestamp);
    }

    public function getOption(string $option, mixed $defaultValue = false): mixed
    {
        return $option === 'date_format' ? 'Y-m-d' : $defaultValue;
    }

    public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
    {
        return [];
    }
}

class FakeTerm
{
    public function __construct(
        public int $object_id,
        public string $taxonomy,
        public string $name
    ) {
    }
}

class FakeTaxonomyTermsProvider implements TaxonomyTermsProviderInterface
{
    public function __construct(private array $terms)
    {
    }
    public function getAllTerms(): array
    {
        return $this->terms;
    }
}

class FakeLabelFormatter implements LabelFormatterInterface
{
    public function formatTermName(string $name): string
    {
        return strtoupper($name);
    }
}

class TableItemsGeneratorTest extends TestCase
{
    public function testGenerateReturnsEmptyArrayIfNoPosts()
    {
        $appearanceConfig = new FakeAppearanceConfig(['post_title'], []);
        $wpService        = new FakeWpService();
        $termsProvider    = new FakeTaxonomyTermsProvider([]);
        $labelFormatter   = new FakeLabelFormatter();
        $generator        = new TableItemsGenerator($appearanceConfig, [], $wpService, $termsProvider, $labelFormatter);
        $this->assertSame([], $generator->generate());
    }

    public function testGenerateReturnsSinglePostWithProperties()
    {
        $post             = new FakePost(1, 'Title', '/link', strtotime('2023-01-01 12:00:00'), ['custom_field' => 'value']);
        $appearanceConfig = new FakeAppearanceConfig(['post_title', 'custom_field', 'post_date'], []);
        $wpService        = new FakeWpService();
        $termsProvider    = new FakeTaxonomyTermsProvider([]);
        $labelFormatter   = new FakeLabelFormatter();
        $generator        = new TableItemsGenerator($appearanceConfig, [$post], $wpService, $termsProvider, $labelFormatter);

        $items = $generator->generate();

        $this->assertCount(1, $items);
        $item = $items[0];
        $this->assertSame(1, $item['id']);
        $this->assertSame('/link', $item['href']);
        $this->assertSame([
            'Title',
            'value',
            date('Y-m-d', strtotime('2023-01-01 12:00:00'))
        ], array_slice($item['columns'], 0, 3));
    }

    public function testGenerateIncludesTaxonomyColumns()
    {
        $post             = new FakePost(2, 'TaxPost', '/taxlink', strtotime('2023-02-02 13:00:00'));
        $appearanceConfig = new FakeAppearanceConfig(['post_title'], ['category']);
        $wpService        = new FakeWpService();
        $termsProvider    = new FakeTaxonomyTermsProvider([new FakeTerm(2, 'category', 'news')]);
        $labelFormatter   = new FakeLabelFormatter();
        $generator        = new TableItemsGenerator($appearanceConfig, [$post], $wpService, $termsProvider, $labelFormatter);

        $items = $generator->generate();

        $this->assertCount(1, $items);
        $item = $items[0];
        $this->assertArrayHasKey('category', $item['columns']);
        $this->assertSame('NEWS', $item['columns']['category']);
    }

    public function testGenerateTaxonomyColumnEmptyIfNoTerms()
    {
        $post             = new FakePost(3, 'NoTermPost', '/noterm', strtotime('2023-03-03 14:00:00'));
        $appearanceConfig = new FakeAppearanceConfig(['post_title'], ['tag']);
        $wpService        = new FakeWpService();
        $termsProvider    = new FakeTaxonomyTermsProvider([]);
        $labelFormatter   = new FakeLabelFormatter();
        $generator        = new TableItemsGenerator($appearanceConfig, [$post], $wpService, $termsProvider, $labelFormatter);

        $items = $generator->generate();
        $item  = $items[0];

        $this->assertArrayHasKey('tag', $item['columns']);
        $this->assertSame('', $item['columns']['tag']);
    }
}
