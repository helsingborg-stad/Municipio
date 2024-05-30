<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\Sources\Services\NullSource;
use Municipio\ExternalContent\Taxonomy\ITaxonomyItem;
use Municipio\ExternalContent\Taxonomy\ITaxonomyRegistrar;
use Municipio\ExternalContent\Taxonomy\NullTaxonomyItem;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Event;
use WP_Error;
use WpService\Contracts\InsertTerm;
use WpService\Contracts\TermExists;

class TermsDecoratorTest extends TestCase
{
    /**
     * @testdox Adds terms to post if $schemaObject property has any corresponding match in supplied $taxonomyItems.
     */
    public function testAddsTermsToPost(): void
    {
        $taxonomyRegistrar        = $this->getTaxonomyRegistrar([$this->getTaxonomyItem()]);
        $schemaObject             = new Event();
        $schemaObject['keywords'] = ['foo', 'bar'];
        $wpService                = $this->getWpService();
        $termsDecorator           = new TermsDecorator($taxonomyRegistrar, $wpService, new WpPostFactory());

        $postData = $termsDecorator->create($schemaObject, $this->getSource());

        $this->assertEquals([1, 2], $postData['tax_input']['test_taxonomy']);
    }

    /**
     * @testdox Uses existing terms if any found.
     */
    public function testAddsExistingTerms(): void
    {
        $taxonomyRegistrar        = $this->getTaxonomyRegistrar([$this->getTaxonomyItem()]);
        $schemaObject             = new Event();
        $schemaObject['keywords'] = ['baz', 'qux'];
        $wpService                = $this->getWpService();
        $termsDecorator           = new TermsDecorator($taxonomyRegistrar, $wpService, new WpPostFactory());

        $postData = $termsDecorator->create($schemaObject, $this->getSource());

        $this->assertEquals([3, 4], $postData['tax_input']['test_taxonomy']);
    }

    private function getTaxonomyItem(): ITaxonomyItem
    {
        return new class extends NullTaxonomyItem {
            public function getSchemaObjectType(): string
            {
                return 'Event';
            }

            public function getName(): string
            {
                return 'test_taxonomy';
            }

            public function getSchemaObjectProperty(): string
            {
                return 'keywords';
            }
        };
    }

    private function getSource(): ISource
    {
        return new NullSource();
    }

    private function getWpService(): InsertTerm&TermExists
    {
        return new class implements InsertTerm, TermExists {
            private int $termsCreatedCount = 0;
            private array $existingTerms   = [
                'test_taxonomy' => [
                    'baz' => ['term_id' => 3],
                    'qux' => ['term_id' => 4]
                ]
            ];

            public function insertTerm(string $term, string $taxonomy = "", array $args = []): array|WP_Error
            {
                $this->termsCreatedCount++;
                return ['term_id' => $this->termsCreatedCount];
            }

            public function termExists(int|string $term, string $taxonomy = "", ?int $parentTerm = null): null|int|array
            {
                return $this->existingTerms[$taxonomy][$term] ?? null;
            }
        };
    }

    private function getTaxonomyRegistrar(array $taxonomyItems): ITaxonomyRegistrar
    {
        return new class ($taxonomyItems) implements ITaxonomyRegistrar {
            public function __construct(private array $taxonomyItems)
            {
            }

            public function addHooks(): void
            {
            }

            public function register(): void
            {
            }

            public function getRegisteredTaxonomyItems(): array
            {
                return $this->taxonomyItems;
            }
        };
    }
}
