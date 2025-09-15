<?php

declare(strict_types=1);

namespace Municipio\Controller\SingularElementarySchool;

use PHPUnit\Framework\TestCase;

class PreambleGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool();
        $generator        = new PreambleGenerator($elementarySchool);
        $this->assertInstanceOf(PreambleGenerator::class, $generator);
    }

    public function testGenerateReturnsStringDescription(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->description('Test description');
        $generator        = new PreambleGenerator($elementarySchool);
        $this->assertSame('Test description', $generator->generate());
    }

    public function testGenerateReturnsFirstArrayDescription(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->description(['First', 'Second']);
        $generator        = new PreambleGenerator($elementarySchool);
        $this->assertSame('First', $generator->generate());
    }

    public function testGenerateReturnsTextObjectDescription(): void
    {
        $textObject       = \Municipio\Schema\Schema::textObject()->text('Text from object');
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->description($textObject);
        $generator        = new PreambleGenerator($elementarySchool);
        $this->assertSame('Text from object', $generator->generate());
    }

    public function testGenerateReturnsNullForNullDescription(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->description(null);
        $generator        = new PreambleGenerator($elementarySchool);
        $this->assertNull($generator->generate());
    }
}
