<?php

namespace Municipio\ExternalContent\Taxonomy;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TaxonomyItemTest extends TestCase
{
    /**
     * @testdox getName() returns a string that is not longer than 32 characters
     */
    public function testGetNameReturnsStringNotLongerThan32Characters()
    {
        $taxonomyItem = new TaxonomyItem(
            'schemaObjectType',
            ['postType'],
            'schemaObjectPropertyThatIsLongerThan32Characters',
            'singleLabel',
            'pluralLabel',
            new FakeWpService()
        );

        $this->assertLessThanOrEqual(32, strlen($taxonomyItem->getName()));
    }

    /**
     * @testdox getName() returns a string that does not contain special characters
     */
    public function testGetNameReturnsStringWithoutSpecialCharacters()
    {
        $taxonomyItem = new TaxonomyItem(
            'schemaObjectType',
            ['postType'],
            '@meta.status',
            'singleLabel',
            'pluralLabel',
            new FakeWpService()
        );

        $this->assertStringNotContainsString('@', $taxonomyItem->getName());
        $this->assertStringNotContainsString('.', $taxonomyItem->getName());
    }

    /**
     * @testdox getName() starts with the schema object type
     */
    public function testGetNameStartsWithSchemaObjectType()
    {
        $taxonomyItem = new TaxonomyItem(
            'Thing',
            ['postType'],
            'schemaObjectProperty',
            'singleLabel',
            'pluralLabel',
            new FakeWpService()
        );

        $this->assertStringStartsWith('thing_', $taxonomyItem->getName());
    }
}
