<?php

namespace Municipio\ExternalContent\Taxonomy;

use Municipio\ExternalContent\Config\SourceTaxonomyConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class TaxonomyItemTest extends TestCase
{
    /**
     * @testdox getName() returns a string that is not longer than 32 characters
     */
    public function testGetNameReturnsStringNotLongerThan32Characters()
    {
        $taxonomyConfig = $this->getTaxonomyConfig([
            'getFromSchemaProperty' => 'schemaObjectPropertyThatIsLongerThan32Characters',
            'getName'               => 'singleLabel',
            'getSingularName'       => 'pluralLabel',
        ]);

        $taxonomyItem = new TaxonomyItem('schemaObjectType', ['postType'], $taxonomyConfig, new FakeWpService());

        $this->assertLessThanOrEqual(32, strlen($taxonomyItem->getName()));
    }

    /**
     * @testdox getName() returns a string that does not contain special characters
     */
    public function testGetNameReturnsStringWithoutSpecialCharacters()
    {
        $taxonomyConfig = $this->getTaxonomyConfig([
            'getFromSchemaProperty' => '@meta.status',
            'getName'               => 'singleLabel',
            'getSingularName'       => 'pluralLabel',
        ]);

        $taxonomyItem = new TaxonomyItem('schemaObjectType', ['postType'], $taxonomyConfig, new FakeWpService());

        $this->assertStringNotContainsString('@', $taxonomyItem->getName());
        $this->assertStringNotContainsString('.', $taxonomyItem->getName());
    }

    /**
     * @testdox getName() starts with the schema object type
     */
    public function testGetNameStartsWithSchemaObjectType()
    {
        $taxonomyConfig = $this->getTaxonomyConfig([
            'getFromSchemaProperty' => 'schemaObjectProperty',
            'getName'               => 'singleLabel',
            'getSingularName'       => 'pluralLabel',
        ]);

        $taxonomyItem = new TaxonomyItem('Thing', ['postType'], $taxonomyConfig, new FakeWpService());

        $this->assertStringStartsWith('thing_', $taxonomyItem->getName());
    }

    /**
     * @testdox getTaxonomyArgs() indicates that the taxonomy is hierarchical if the taxonomy config says so
     */
    public function testGetTaxonomyArgsIndicatesHierarchicalIfTaxonomyConfigSaysSo()
    {
        $taxonomyConfig = $this->getTaxonomyConfig([
            'getFromSchemaProperty' => 'schemaObjectProperty',
            'getName'               => 'singleLabel',
            'getSingularName'       => 'pluralLabel',
            'isHierarchical'        => true,
        ]);

        $taxonomyItem = new TaxonomyItem('schemaObjectType', ['postType'], $taxonomyConfig, new FakeWpService(['__' => fn($label) => $label]));

        $this->assertTrue($taxonomyItem->getTaxonomyArgs()['hierarchical']);
    }

    private function getTaxonomyConfig($returnValuesByMethod = []): SourceTaxonomyConfigInterface|MockObject
    {
        $taxonomyConfig = $this->createMock(SourceTaxonomyConfigInterface::class);

        foreach ($returnValuesByMethod as $method => $returnValue) {
            $taxonomyConfig->method($method)->willReturn($returnValue);
        }

        return $taxonomyConfig;
    }
}
