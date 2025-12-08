<?php

namespace Municipio\Controller\School\Preschool;

use PHPUnit\Framework\TestCase;
use Municipio\Schema\Schema;
use WpService\Contracts\Wpautop;

class AccordionListItemsGeneratorTest extends TestCase
{
    public function testGenerateReturnsNullIfDescriptionIsNotArray()
    {
        $school    = Schema::preschool()->description('not-an-array');
        $generator = new AccordionListItemsGenerator($school, $this->getWpService());

        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsNullIfDescriptionArrayHasOneOrZeroItems()
    {
        $school    = Schema::preschool()->description([]);
        $generator = new AccordionListItemsGenerator($school, $this->getWpService());
        $this->assertNull($generator->generate());

        $school    = Schema::preschool()->description([Schema::textObject()->headline('h')->text('t')]);
        $generator = new AccordionListItemsGenerator($school, $this->getWpService());
        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsAccordionItemsForValidTextObjects()
    {
        $school = Schema::preschool()->description([
            Schema::textObject()->headline('Headline 1')->text('Text 1'),
            Schema::textObject()->headline('Headline 2')->text('Text 2'),
            Schema::textObject()->headline('Headline 3')->text('Text 3')
        ]);

        $generator = new AccordionListItemsGenerator($school, $this->getWpService());
        $result    = $generator->generate();

        $this->assertCount(2, $result);
        $this->assertEquals([
            ['heading' => 'Headline 2', 'content' => 'Text 2'],
            ['heading' => 'Headline 3', 'content' => 'Text 3'],
        ], array_values($result));
    }

    public function testGenerateSkipsNonTextObjectItems()
    {
        $school = Schema::preschool()->description([
            Schema::textObject()->headline('Headline 1')->text('Text 1'),
            Schema::textObject()->headline('Headline 2')->text('Text 2'),
            Schema::textObject()->headline('Headline 3')->text('Text 3')
        ]);

        $generator = new AccordionListItemsGenerator($school, $this->getWpService());
        $result    = $generator->generate();

        $this->assertCount(2, $result);
        $this->assertEquals([
            ['heading' => 'Headline 2', 'content' => 'Text 2'],
            ['heading' => 'Headline 3', 'content' => 'Text 3'],
        ], array_values($result));
    }

    private function getWpService(): Wpautop
    {
        return new class implements Wpautop {
            public function wpautop(string $text, bool $br = true): string
            {
                return $text;
            }
        };
    }
}
