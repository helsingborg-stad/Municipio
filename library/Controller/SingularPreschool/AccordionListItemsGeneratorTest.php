<?php

namespace Municipio\Controller\SingularPreschool;

use PHPUnit\Framework\TestCase;
use Municipio\Schema\Schema;

class AccordionListItemsGeneratorTest extends TestCase
{
    public function testGenerateReturnsNullIfDescriptionIsNotArray()
    {
        $school    = Schema::elementarySchool()->description('not-an-array');
        $generator = new AccordionListItemsGenerator($school);

        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsNullIfDescriptionArrayHasOneOrZeroItems()
    {
        $school    = Schema::elementarySchool()->description([]);
        $generator = new AccordionListItemsGenerator($school);
        $this->assertNull($generator->generate());

        $school    = Schema::elementarySchool()->description([Schema::textObject()->headline('h')->text('t')]);
        $generator = new AccordionListItemsGenerator($school);
        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsAccordionItemsForValidTextObjects()
    {
        $school = Schema::elementarySchool()->description([
            Schema::textObject()->headline('Headline 1')->text('Text 1'),
            Schema::textObject()->headline('Headline 2')->text('Text 2'),
            Schema::textObject()->headline('Headline 3')->text('Text 3')
        ]);

        $generator = new AccordionListItemsGenerator($school);
        $result    = $generator->generate();

        $this->assertCount(2, $result);
        $this->assertEquals([
            ['heading' => 'Headline 2', 'content' => 'Text 2'],
            ['heading' => 'Headline 3', 'content' => 'Text 3'],
        ], array_values($result));
    }

    public function testGenerateSkipsNonTextObjectItems()
    {
        $school = Schema::elementarySchool()->description([
            Schema::textObject()->headline('Headline 1')->text('Text 1'),
            Schema::textObject()->headline('Headline 2')->text('Text 2'),
            Schema::textObject()->headline('Headline 3')->text('Text 3')
        ]);

        $generator = new AccordionListItemsGenerator($school);
        $result    = $generator->generate();

        $this->assertCount(2, $result);
        $this->assertEquals([
            ['heading' => 'Headline 2', 'content' => 'Text 2'],
            ['heading' => 'Headline 3', 'content' => 'Text 3'],
        ], array_values($result));
    }
}
