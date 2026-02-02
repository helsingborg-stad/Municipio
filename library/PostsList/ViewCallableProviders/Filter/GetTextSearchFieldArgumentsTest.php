<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Post_Type;
use WpService\Contracts\__;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetPostTypeObject;

class GetTextSearchFieldArgumentsTest extends TestCase
{
    #[TestDox('returns an array of text search field arguments')]
    public function testReturnsArrayOfTextSearchFieldArguments(): void
    {
        $getTextSearchFieldArguments = new GetTextSearchFieldArguments(new DefaultGetPostsConfig(), 's', $this->createWpService());

        $this->assertArrayContainsPartial([
            'type'     => 'search',
            'name'     => 's',
            'label'    => 'Search',
            'required' => false,
        ], $getTextSearchFieldArguments->getCallable()());
    }

    #[TestDox('label is adapted to post type if only 1 post type is targeted')]
    public function testLabelIsAdaptedToPostTypeIfOnlyOnePostTypeIsTargeted(): void
    {
        $getPostsConfig = new class extends DefaultGetPostsConfig {
            public function getPostTypes(): array
            {
                return ['article'];
            }
        };

        $postTypeObject        = new WP_Post_Type([]);
        $postTypeObject->name  = 'article';
        $postTypeObject->label = 'Articles';

        $getTextSearchFieldArguments = new GetTextSearchFieldArguments($getPostsConfig, 's', $this->createWpService($postTypeObject));

        $this->assertArrayContainsPartial([
            'label' => 'Search Articles',
        ], $getTextSearchFieldArguments->getCallable()());
    }

    #[TestDox('returns default search label if more than 1 post type is targeted')]
    public function testReturnsDefaultSearchLabelIfMoreThanOnePostTypeIsTargeted(): void
    {
        $getPostsConfig = new class extends DefaultGetPostsConfig {
            public function getPostTypes(): array
            {
                return ['article', 'news'];
            }
        };

        $getTextSearchFieldArguments = new GetTextSearchFieldArguments($getPostsConfig, 's', $this->createWpService());

        $this->assertArrayContainsPartial([
            'label' => 'Search',
        ], $getTextSearchFieldArguments->getCallable()());
    }

    #[TestDox('contains value if search is provided in posts config')]
    public function testContainsValueIfSearchIsProvidedInPostsConfig(): void
    {
        $getPostsConfig = new class extends DefaultGetPostsConfig {
            public function getSearch(): ?string
            {
                return 'example search';
            }
        };

        $getTextSearchFieldArguments = new GetTextSearchFieldArguments($getPostsConfig, 's', $this->createWpService());

        $this->assertArrayContainsPartial([
            'value' => 'example search',
        ], $getTextSearchFieldArguments->getCallable()());
    }

    #[TestDox('does not contain value if search is null in posts config')]
    public function testDoesNotContainValueIfSearchIsNullInPostsConfig(): void
    {
        $getPostsConfig = new class extends DefaultGetPostsConfig {
            public function getSearch(): ?string
            {
                return null;
            }
        };

        $getTextSearchFieldArguments = new GetTextSearchFieldArguments($getPostsConfig, 's', $this->createWpService());

        $this->assertArrayNotHasKey('value', $getTextSearchFieldArguments->getCallable()());
    }

    private function createWpService(?WP_Post_Type $postTypeObject = null): __&GetPostTypeObject&ApplyFilters
    {
        return new class ($postTypeObject) implements __, GetPostTypeObject, ApplyFilters {
            public function __construct(private $postTypeObject)
            {
            }
            public function __(string $text, string $domain = 'default'): string
            {
                return $text;
            }
            public function getPostTypeObject(string $postType): ?WP_Post_Type
            {
                return $this->postTypeObject && $this->postTypeObject->name === $postType
                    ? $this->postTypeObject
                    : null;
            }
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };
    }

    private function assertArrayContainsPartial(array $expectedSubset, array $actualArray): void
    {
        foreach ($expectedSubset as $key => $value) {
            $this->assertEquals($value, $actualArray[$key]);
        }
    }
}
