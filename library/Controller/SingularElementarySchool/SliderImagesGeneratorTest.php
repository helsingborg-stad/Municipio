<?php

declare(strict_types=1);

namespace Municipio\Controller\SingularElementarySchool;

use PHPUnit\Framework\TestCase;

class SliderImagesGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool();
        $generator        = new SliderImagesGenerator($elementarySchool);
        $this->assertInstanceOf(SliderImagesGenerator::class, $generator);
    }

    public function testGenerateReturnsNullIfImageIsNotArray(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->image(null);
        $generator        = new SliderImagesGenerator($elementarySchool);
        $this->assertNull($generator->generate());
    }

    public function testGenerateReturnsNullIfImageArrayIsEmpty(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->image([]);
        $generator        = new SliderImagesGenerator($elementarySchool);
        $this->assertNull($generator->generate());
    }
}
