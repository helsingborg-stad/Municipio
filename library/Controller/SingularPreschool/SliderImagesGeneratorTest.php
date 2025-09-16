<?php

declare(strict_types=1);

namespace Municipio\Controller\SingularPreschool;

use PHPUnit\Framework\TestCase;

class SliderImagesGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool();
        $generator = new SliderImagesGenerator($preschool);
        $this->assertInstanceOf(SliderImagesGenerator::class, $generator);
    }

    public function testGenerateReturnsNullIfImageIsNotArray(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->image(null);
        $generator = new SliderImagesGenerator($preschool);
        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsNullIfImageArrayIsEmpty(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->image([]);
        $generator = new SliderImagesGenerator($preschool);
        $this->assertNull($generator->generate());
    }
}
