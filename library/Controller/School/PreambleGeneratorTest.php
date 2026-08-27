<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;

class PreambleGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool();
        $generator        = new PreambleGenerator($elementarySchool);
        $this->assertInstanceOf(PreambleGenerator::class, $generator);
    }

    public function testGenerateReturnsFirstArrayDescription(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->description([
            Schema::textObject()->text('First description'),
            Schema::textObject()->text('Preamble')->name('role:preamble')
        ]);

        $generator = new PreambleGenerator($elementarySchool);

        $this->assertSame('Preamble', $generator->generate());
    }

    public function testGenerateReturnsNullForNullDescription(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->description(null);
        $generator        = new PreambleGenerator($elementarySchool);
        $this->assertNull($generator->generate());
    }
}
