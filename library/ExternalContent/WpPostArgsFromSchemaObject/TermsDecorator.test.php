<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\Source;
use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\Taxonomy\TaxonomyItemInterface;
use Municipio\ExternalContent\Taxonomy\NullTaxonomyItem;
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
        $taxonomyItem = $this->getTaxonomyItem('Event', 'actor', 'test_taxonomy');

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
        $taxonomyItem = $this->getTaxonomyItem('Event', 'actor.callSign', 'test_taxonomy');

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostFactory());
        $termsDecorator->create($schemaObject, $this->getSource());

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
        $taxonomyItem = $this->getTaxonomyItem('Event', '@meta.illness', 'test_taxonomy');

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostFactory());
        $termsDecorator->create($schemaObject, $this->getSource());

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
        $taxonomyItem = $this->getTaxonomyItem('Event', '@meta.illness', 'test_taxonomy');

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostFactory());
        $termsDecorator->create($schemaObject, $this->getSource());

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
        $taxonomyItem = $this->getTaxonomyItem('Event', '@meta.foo', 'test_taxonomy');

        $termsDecorator = new TermsDecorator([$taxonomyItem], $termFactory, $wpService, new WpPostFactory());
        $termsDecorator->create($schemaObject, $this->getSource());

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
                return WpMockFactory::createWpTerm(['term_id' => 3, 'name' => $schemaObject]);
            }
        };
    }

    private function getTaxonomyItem(
        string $type = 'Event',
        string $property = 'keywords',
        string $name = 'test_taxonomy'
    ): TaxonomyItemInterface {
        return new class ($type, $property, $name) extends NullTaxonomyItem {
            public function __construct(
                private string $type,
                private string $property,
                private string $name
            ) {
            }
            public function getSchemaObjectType(): string
            {
                return $this->type;
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getSchemaObjectProperty(): string
            {
                return $this->property;
            }
        };
    }

    private function getSource(): SourceInterface
    {
        return new Source('test_post_type', 'Thing');
    }
}
