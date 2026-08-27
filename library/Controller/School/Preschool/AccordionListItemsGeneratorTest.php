<?php

namespace Municipio\Controller\School\Preschool;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\Wpautop;

class AccordionListItemsGeneratorTest extends TestCase
{
    public function testGenerateReturnsNullIfDescriptionIsNotArray()
    {
        $school = Schema::preschool()->description('not-an-array');
        $generator = new AccordionListItemsGenerator($school, $this->getWpService());

        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsNullIfDescriptionArrayHasOneOrZeroItems()
    {
        $school = Schema::preschool()->description([]);
        $generator = new AccordionListItemsGenerator($school, $this->getWpService());
        $this->assertNull($generator->generate());

        $school = Schema::preschool()->description([Schema::textObject()->headline('h')->text('t')]);
        $generator = new AccordionListItemsGenerator($school, $this->getWpService());
        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsAccordionItemsForValidTextObjects()
    {
        $school = Schema::preschool()->description([
            Schema::textObject()->headline('Headline 1')->text('Text 1'),
            Schema::textObject()->headline('Headline 2')->text('Text 2'),
            Schema::textObject()->headline('Headline 3')->text('Text 3'),
        ]);

        $generator = new AccordionListItemsGenerator($school, $this->getWpService());
        $result = $generator->generate();

        $this->assertCount(3, $result);
        $this->assertEquals(
            [
                ['heading' => 'Headline 1', 'content' => 'Text 1'],
                ['heading' => 'Headline 2', 'content' => 'Text 2'],
                ['heading' => 'Headline 3', 'content' => 'Text 3'],
            ],
            array_values($result),
        );
    }

    public function testGenerateSkipsNonTextObjectItems()
    {
        $school = Schema::preschool()->description([
            Schema::textObject()->headline('Headline 1')->text('Text 1'),
            Schema::textObject()->headline('Headline 2')->text('Text 2')->name('role:preamble'),
            Schema::textObject()->headline('Headline 3')->text('Text 3')->name('role:alert'),
            Schema::textObject()->headline('Headline 4')->text('Text 4')->name('visit_us'),
            Schema::textObject()->headline('Headline 5')->text('Text 5'),
        ]);

        $generator = new AccordionListItemsGenerator($school, $this->getWpService());
        $result = $generator->generate();

        $this->assertCount(2, $result);
        $this->assertEquals(
            [
                ['heading' => 'Headline 1', 'content' => 'Text 1'],
                ['heading' => 'Headline 5', 'content' => 'Text 5'],
            ],
            array_values($result),
        );
    }

    private function getWpService(): Wpautop&ApplyFilters
    {
        return new class implements Wpautop, ApplyFilters {
            public function wpautop(string $text, bool $br = true): string
            {
                return $text;
            }

            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };
    }
}
