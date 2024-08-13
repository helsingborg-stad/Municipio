<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\Sources\Services\NullSource;
use Municipio\ExternalContent\Taxonomy\ITaxonomyItem;
use Municipio\ExternalContent\Taxonomy\ITaxonomyRegistrar;
use Municipio\ExternalContent\Taxonomy\NullTaxonomyItem;
use Municipio\ExternalContent\WpTermFactory\WpTermFactory;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Event;
use WpService\Implementations\FakeWpService;

class TermsDecoratorTest extends TestCase
{
    /**
     * @testdox Adds terms to post if $schemaObject property has any corresponding match in supplied $taxonomyItems.
     */
    public function testAddsTermsToPost(): void
    {
        $taxonomyRegistrar        = $this->getTaxonomyRegistrar([$this->getTaxonomyItem()]);
        $schemaObject             = new Event();
        $schemaObject['keywords'] = ['foo'];
        $wpService                = new FakeWpService(['termExists' => null, 'insertTerm' => ['term_id' => 1]]);
        $termsDecorator           = new TermsDecorator($taxonomyRegistrar, new WpTermFactory(), $wpService, new WpPostFactory());

        $postData = $termsDecorator->create($schemaObject, $this->getSource());

        $this->assertEquals([1], $postData['tax_input']['test_taxonomy']);
    }

    /**
     * @testdox Uses existing terms if any found.
     */
    public function testAddsExistingTerms(): void
    {
        $taxonomyRegistrar        = $this->getTaxonomyRegistrar([$this->getTaxonomyItem()]);
        $schemaObject             = new Event();
        $schemaObject['keywords'] = ['baz'];
        $wpService                = new FakeWpService(['termExists' => ['term_id' => 3]]);
        $termsDecorator           = new TermsDecorator(
            $taxonomyRegistrar,
            new WpTermFactory(),
            $wpService,
            new WpPostFactory()
        );

        $postData = $termsDecorator->create($schemaObject, $this->getSource());

        $this->assertEquals([3], $postData['tax_input']['test_taxonomy']);
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
