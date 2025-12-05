<?php

namespace Municipio\PostsList\ViewCallableProviders\Table\TableArguments;

use Municipio\PostObject\NullPostObject;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Post_Type;
use WpService\Contracts\GetPostTypeObject;
use WpService\Contracts\GetTaxonomies;
use WpService\Contracts\__;

class TableHeadingsGeneratorTest extends TestCase
{
    #[TestDox('post_title column is labeled with post type singular name')]
    public function testGenerateReturnsCorrectHeadings(): void
    {
        $appearanceConfig = $this->getAppearanceConfig(['post_title'], []);
        $post             = $this->createPostWithType('test_post_type');
        $posts            = [$post];

        $postTypeObject         = new WP_Post_Type([]);
        $postTypeObject->labels = new class {
            public string $singular_name = 'Test Post Type';
        };
        $wpService              = $this->getWpService($postTypeObject);
        $tableHeadingsGenerator = new TableHeadingsGenerator($appearanceConfig, $posts, $wpService);

        $headings = $tableHeadingsGenerator->generate();

        $this->assertEquals(['Test Post Type'], $headings);
    }

    #[TestDox('post_title column is labeled "Title" when post type singular name is not available')]
    public function testGenerateReturnsTitleWhenNoSingularName(): void
    {
        $appearanceConfig       = $this->getAppearanceConfig(['post_title'], []);
        $post                   = $this->createPostWithType('test_post_type');
        $posts                  = [$post];
        $wpService              = $this->getWpService();
        $tableHeadingsGenerator = new TableHeadingsGenerator($appearanceConfig, $posts, $wpService);

        $headings = $tableHeadingsGenerator->generate();

        $this->assertEquals(['Title'], $headings);
    }

    #[TestDox('post_title column is labeled "Title" when more than one post type is present')]
    public function testGenerateReturnsTitleWhenMultiplePostTypes(): void
    {
        $appearanceConfig       = $this->getAppearanceConfig(['post_title'], []);
        $post1                  = $this->createPostWithType('test_post_type_1');
        $post2                  = $this->createPostWithType('test_post_type_2');
        $posts                  = [$post1, $post2];
        $postTypeObject         = new WP_Post_Type([]);
        $postTypeObject->labels = new class {
            public string $singular_name = 'Test Post Type';
        };
        $wpService              = $this->getWpService($postTypeObject);
        $tableHeadingsGenerator = new TableHeadingsGenerator($appearanceConfig, $posts, $wpService);

        $headings = $tableHeadingsGenerator->generate();

        $this->assertEquals(['Title'], $headings);
    }

    #[TestDox('post_date column is labeled "Published"')]
    public function testGenerateReturnsPublishedHeading(): void
    {
        $appearanceConfig = $this->getAppearanceConfig(['post_date'], []);
        $posts            = [new NullPostObject()];
        $wpService        = $this->getWpService(null);

        $tableHeadingsGenerator = new TableHeadingsGenerator($appearanceConfig, $posts, $wpService);
        $headings               = $tableHeadingsGenerator->generate();

        $this->assertEquals(['Published'], $headings);
    }

    #[TestDox('unknown columns are labeled with capitalized property names')]
    public function testGenerateReturnsCapitalizedPropertyNames(): void
    {
        $appearanceConfig = $this->getAppearanceConfig(['unknown_column'], []);
        $posts            = [new NullPostObject()];
        $wpService        = $this->getWpService(null);

        $tableHeadingsGenerator = new TableHeadingsGenerator($appearanceConfig, $posts, $wpService);
        $headings               = $tableHeadingsGenerator->generate();

        $this->assertEquals(['Unknown column'], $headings);
    }

    #[TestDox('taxonomy columns are labeled with taxonomy singular names')]
    public function testGenerateReturnsTaxonomyHeadings(): void
    {
        $appearanceConfig = $this->getAppearanceConfig([], ['category']);
        $posts            = [new NullPostObject()];
        $wpService        = $this->getWpService(null, [
            'category' => new class {
                public object $labels;
                public function __construct()
                {
                    $this->labels = new class {
                        public string $singular_name = 'Category';
                    };
                }
            },
        ]);

        $tableHeadingsGenerator = new TableHeadingsGenerator($appearanceConfig, $posts, $wpService);
        $headings               = $tableHeadingsGenerator->generate();

        $this->assertEquals(['Category'], $headings);
    }

    private function getAppearanceConfig(array $postPropertiesToDisplay = [], array $taxonomiesToDisplay = []): AppearanceConfigInterface
    {
        return new class ($postPropertiesToDisplay, $taxonomiesToDisplay) extends DefaultAppearanceConfig {
            public function __construct(private array $postPropertiesToDisplay, private array $taxonomiesToDisplay)
            {
            }

            public function getPostPropertiesToDisplay(): array
            {
                return $this->postPropertiesToDisplay;
            }

            public function getTaxonomiesToDisplay(): array
            {
                return $this->taxonomiesToDisplay;
            }
        };
    }

    private function getWpService(?WP_Post_Type $postTypeObject = null, ?array $taxonomies = []): GetPostTypeObject&GetTaxonomies&__
    {
        return new class ($postTypeObject, $taxonomies) implements GetPostTypeObject, GetTaxonomies, __ {
            public function __construct(private ?WP_Post_Type $postTypeObject, private ?array $taxonomies)
            {
            }

            public function getPostTypeObject(string $postType): ?WP_Post_Type
            {
                return $this->postTypeObject;
            }

            public function getTaxonomies(array $args = [], string $output = 'names', string $operator = 'and'): array
            {
                return $this->taxonomies;
            }

            public function __(string $text, string $domain = 'default'): string
            {
                return $text;
            }
        };
    }

    private function createPostWithType(string $type): NullPostObject
    {
        return new class ($type) extends NullPostObject {
            public function __construct(private string $type)
            {
            }
            public function getPostType(): string
            {
                return $this->type;
            }
        };
    }
}
