<?php

declare(strict_types=1);

namespace Municipio\Controller\SingularPreschool;

use PHPUnit\Framework\TestCase;

class PreambleGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool();
        $generator = new PreambleGenerator($preschool);
        $this->assertInstanceOf(PreambleGenerator::class, $generator);
    }

    public function testGenerateReturnsStringDescription(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->description('Test description');
        $generator = new PreambleGenerator($preschool);
        $this->assertSame('Test description', $generator->generate());
    }

    public function testGenerateReturnsFirstArrayDescription(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->description(['First', 'Second']);
        $generator = new PreambleGenerator($preschool);
        $this->assertSame('First', $generator->generate());
    }

    public function testGenerateReturnsTextObjectDescription(): void
    {
        $textObject = \Municipio\Schema\Schema::textObject()->text('Text from object');
        $preschool  = \Municipio\Schema\Schema::preschool()->description($textObject);
        $generator  = new PreambleGenerator($preschool);
        $this->assertSame('Text from object', $generator->generate());
    }

    public function testGenerateReturnsNullForNullDescription(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->description(null);
        $generator = new PreambleGenerator($preschool);
        $this->assertNull($generator->generate());
    }
}
