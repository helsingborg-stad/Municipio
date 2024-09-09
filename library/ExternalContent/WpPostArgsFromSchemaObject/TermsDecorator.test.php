<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\Source;
use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\Taxonomy\TaxonomyItemInterface;
use Municipio\ExternalContent\Taxonomy\TaxonomyRegistrarInterface;
use Municipio\ExternalContent\Taxonomy\NullTaxonomyItem;
use Municipio\ExternalContent\WpTermFactory\WpTermFactory;
use Municipio\ExternalContent\WpTermFactory\WpTermFactoryInterface;
use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Event;
use WP_Term;
use WpService\Implementations\FakeWpService;

class TermsDecoratorTest extends TestCase
{
    /**
     * @testdox Adds terms to post if $schemaObject property has any corresponding match in supplied $taxonomyItems.
     */
    public function testAddsTermsToPost(): void
    {
        $schemaObject             = new Event();
        $schemaObject['keywords'] = ['foo'];
        $wpService                = new FakeWpService(['termExists' => null, 'insertTerm' => ['term_id' => 1]]);
        $termsDecorator           = new TermsDecorator(
            [$this->getTaxonomyItem()],
            $this->getWpTermFactory(),
            $wpService,
            new WpPostFactory()
        );

        $postData = $termsDecorator->create($schemaObject, $this->getSource());

        $this->assertEquals([1], $postData['tax_input']['test_taxonomy']);
    }

    /**
     * @testdox Uses existing terms if any found.
     */
    public function testAddsExistingTerms(): void
    {
        $schemaObject             = new Event();
        $schemaObject['keywords'] = ['baz'];
        $wpService                = new FakeWpService(['termExists' => ['term_id' => 3]]);
        $termsDecorator           = new TermsDecorator(
            [$this->getTaxonomyItem()],
            $this->getWpTermFactory(),
            $wpService,
            new WpPostFactory()
        );

        $postData = $termsDecorator->create($schemaObject, $this->getSource());

        $this->assertEquals([3], $postData['tax_input']['test_taxonomy']);
    }

    private function getWpTermFactory(): WpTermFactoryInterface
    {
        return new class implements WpTermFactoryInterface {
            public function create(BaseType|string $schemaObject, string $taxonomy): WP_Term
            {
                return WpMockFactory::createWpTerm(['term_id' => 3, 'name' => $schemaObject]);
            }
        };
    }

    private function getTaxonomyItem(): TaxonomyItemInterface
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

    private function getSource(): SourceInterface
    {
        return new Source('test_post_type', 'Thing');
    }
}
