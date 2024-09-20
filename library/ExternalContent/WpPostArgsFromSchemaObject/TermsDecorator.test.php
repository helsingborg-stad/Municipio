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
use Spatie\SchemaOrg\Schema;
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

    /**
     * @testdox Can create terms from schema property that contains other schema types.
     */
    public function testCanCreateTermsFromSchemaPropertyThatContainsOtherSchemaTypes(): void
    {
        $schemaObject = new Event();
        $schemaObject->actor(Schema::person()->name('testPerson'));
        $wpService    = new FakeWpService(['termExists' => ['term_id' => 3]]);
        $termFactory  = $this->getWpTermFactory();
        $taxonomyItem = new class extends NullTaxonomyItem {
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
                return 'actor';
            }
        };

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostFactory());
        $termsDecorator->create($schemaObject, $this->getSource());

        $this->assertEquals('testPerson', $termFactory->calls[0][0]);
    }

    /**
     * @testdox Can create terms from nested schema property.
     */
    public function testCanCreateTermsFromNestedSchemaProperty(): void
    {
        $schemaObject = new Event();
        $schemaObject->actor(Schema::person()->name('Heath Ledger')->callSign('The Joker'));
        $wpService    = new FakeWpService(['termExists' => ['term_id' => 3]]);
        $termFactory  = $this->getWpTermFactory();
        $taxonomyItem = new class extends NullTaxonomyItem {
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
                return 'actor.callSign';
            }
        };

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostFactory());
        $termsDecorator->create($schemaObject, $this->getSource());

        $this->assertEquals('The Joker', $termFactory->calls[0][0]);
    }

    /**
     * @testdox Can create terms from nested schema property.
     */
    public function testCanCreateTermsFromMetaPropertyValue(): void
    {
        $schemaObject = new Event();
        $schemaObject->setProperty('@meta', [Schema::propertyValue()->name('illness')->value('Mental')]);
        $wpService    = new FakeWpService(['termExists' => ['term_id' => 3]]);
        $termFactory  = $this->getWpTermFactory();
        $taxonomyItem = new class extends NullTaxonomyItem {
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
                return '@meta.illness';
            }
        };

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostFactory());
        $termsDecorator->create($schemaObject, $this->getSource());

        $this->assertEquals('Mental', $termFactory->calls[0][0]);
    }

    private function getWpTermFactory(): WpTermFactoryInterface
    {
        return new class implements WpTermFactoryInterface {
            public array $calls = [];
            public function create(BaseType|string $schemaObject, string $taxonomy): WP_Term
            {
                $this->calls[] = func_get_args();
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
