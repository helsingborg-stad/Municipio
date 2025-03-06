<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Config\SourceTaxonomyConfigInterface;
use Municipio\ExternalContent\WpTermFactory\WpTermFactoryInterface;
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
        $wpService                = new FakeWpService(['termExists' => null, 'wpInsertTerm' => ['term_id' => 1] ]);
        $termsDecorator           = new TermsDecorator(
            [$this->getTaxonomyItem()],
            $this->getWpTermFactory(),
            $wpService,
            new WpPostArgsFromSchemaObject()
        );

        $postData = $termsDecorator->transform($schemaObject);

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
            new WpPostArgsFromSchemaObject()
        );

        $postData = $termsDecorator->transform($schemaObject);

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
        $taxonomyItem = $this->getTaxonomyItem('actor', 'test_taxonomy');

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostArgsFromSchemaObject());
        $termsDecorator->transform($schemaObject);

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
        $taxonomyItem = $this->getTaxonomyItem('actor.callSign', 'test_taxonomy');

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostArgsFromSchemaObject());
        $termsDecorator->transform($schemaObject);

        $this->assertEquals('The Joker', $termFactory->calls[0][0]);
    }

    /**
     * @testdox Can create terms from nested schema property array.
     */
    public function testCanCreateTermsFromMetaPropertyValueArray(): void
    {
        $schemaObject = new Event();
        $schemaObject->setProperty('@meta', [Schema::propertyValue()->name('illness')->value('Mental')]);
        $wpService    = new FakeWpService(['termExists' => ['term_id' => 3]]);
        $termFactory  = $this->getWpTermFactory();
        $taxonomyItem = $this->getTaxonomyItem('@meta.illness', 'test_taxonomy');

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostArgsFromSchemaObject());
        $termsDecorator->transform($schemaObject);

        $this->assertEquals('Mental', $termFactory->calls[0][0]);
    }

    /**
     * @testdox Can create terms from nested schema property.
     */
    public function testCanCreateTermsFromMetaPropertyValue(): void
    {
        $schemaObject = new Event();
        $schemaObject->setProperty('@meta', Schema::propertyValue()->name('illness')->value('Mental'));
        $wpService    = new FakeWpService(['termExists' => ['term_id' => 3]]);
        $termFactory  = $this->getWpTermFactory();
        $taxonomyItem = $this->getTaxonomyItem('@meta.illness', 'test_taxonomy');

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostArgsFromSchemaObject());
        $termsDecorator->transform($schemaObject);

        $this->assertEquals('Mental', $termFactory->calls[0][0]);
    }

    /**
     * @testdox Does not create from PropertyValue if name is not the property name.
     */
    public function testDoesNotCreateFromPropertyValueIfNameIsNotThePropertyName(): void
    {

        $schemaObject = new Event();
        $schemaObject->setProperty('@meta', [
            Schema::propertyValue()->name('foo')->value('Bar'),
            Schema::propertyValue()->name('illness')->value('Mental'),
        ]);
        $wpService    = new FakeWpService(['termExists' => ['term_id' => 3]]);
        $termFactory  = $this->getWpTermFactory();
        $taxonomyItem = $this->getTaxonomyItem('@meta.foo', 'test_taxonomy');

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostArgsFromSchemaObject());
        $termsDecorator->transform($schemaObject);

        $this->assertCount(1, $termFactory->calls);
        $this->assertEquals('Bar', $termFactory->calls[0][0]);
    }

    private function getWpTermFactory(): WpTermFactoryInterface
    {
        return new class implements WpTermFactoryInterface {
            public array $calls = [];
            public function create(BaseType|string $schemaObject, string $taxonomy): WP_Term
            {
                $this->calls[] = func_get_args();
                $term          = new WP_Term([]);
                $term->term_id = 3;
                $term->name    = $schemaObject;
                return $term;
            }
        };
    }

    private function getTaxonomyItem(
        string $property = 'keywords',
        string $name = 'test_taxonomy'
    ): SourceTaxonomyConfigInterface {
        return new class ($property, $name) implements SourceTaxonomyConfigInterface {
            public function __construct(
                private string $property,
                private string $name
            ) {
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getFromSchemaProperty(): string
            {
                return $this->property;
            }

            public function getSingularName(): string
            {
                return '';
            }

            public function getPluralName(): string
            {
                return '';
            }

            public function isHierarchical(): bool
            {
                return false;
            }
        };
    }
}
