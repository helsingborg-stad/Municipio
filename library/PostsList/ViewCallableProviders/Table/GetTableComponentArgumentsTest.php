<?php

namespace Municipio\PostsList\ViewCallableProviders\Table;

use Municipio\PostObject\NullPostObject;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class GetTableComponentArgumentsTest extends TestCase
{
    #[TestDox('returns array with headings and list')]
    public function testGetTableArguments(): void
    {
        $wpService = new FakeWpService(['applyFilters' => static fn(string $hookName, array $arguments): array => $arguments]);
        $getTableComponentArguments = new GetTableComponentArguments([], new DefaultAppearanceConfig(), $wpService);
        $callable                   = $getTableComponentArguments->getCallable();
        $result                     = $callable();

        $this->assertIsArray($result);
        $this->isArray($result['headings']);
        $this->isArray($result['list']);
    }

    #[TestDox('filters generated table arguments before cell formatting')]
    public function testFiltersGeneratedTableArgumentsBeforeCellFormatting(): void
    {
        $post = new class extends NullPostObject {
            public function getId(): int
            {
                return 123;
            }

            public function getPermalink(): string
            {
                return 'https://example.test/post';
            }

            public function getPostType(): string
            {
                return 'example';
            }

            public function __get(string $key): mixed
            {
                return [
                    'first_field'  => 'First value',
                    'second_field' => 'Second value',
                ][$key] ?? null;
            }
        };
        $appearanceConfig = new class extends DefaultAppearanceConfig {
            public function getPostPropertiesToDisplay(): array
            {
                return ['first_field', 'second_field'];
            }
        };
        $wpService = new FakeWpService([
            'applyFilters' => static function (string $hookName, array $arguments, array $posts, array $postTypes) use ($post): array {
                static::assertSame(GetTableComponentArguments::FILTER_HOOK, $hookName);
                static::assertSame(['First field', 'Second field'], $arguments['headings']);
                static::assertSame(['First value', 'Second value'], $arguments['list'][0]['columns']);
                static::assertSame([$post], $posts);
                static::assertSame(['example'], $postTypes);

                $arguments['headings'][1] = 'Filtered heading';
                $arguments['list'][0]['columns'][1] = 'Filtered value';

                return $arguments;
            },
        ]);

        $result = (new GetTableComponentArguments([$post], $appearanceConfig, $wpService, ['example']))->getCallable()();

        static::assertSame(['First field', 'Filtered heading'], $result['headings']);
        static::assertSame(123, $result['list'][0]['id']);
        static::assertSame('https://example.test/post', $result['list'][0]['href']);
        static::assertSame('First value', $result['list'][0]['columns'][0]);
        static::assertSame(
            '<span class="c-typography c-typography__variant--meta">Filtered value</span>',
            $result['list'][0]['columns'][1],
        );
    }
}
