<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AppearanceConfigBuilderTest extends TestCase
{
    #[TestDox('It should build an AppearanceConfig with the correct properties')]
    public function testBuildAppearanceConfig(): void
    {
        $builder = (new AppearanceConfigBuilder())
            ->setNumberOfColumns(3)
            ->setShouldDisplayFeaturedImage(true)
            ->setShouldDisplayReadingTime(true)
            ->setTaxonomiesToDisplay(['category', 'tag'])
            ->setPostPropertiesToDisplay(['author', 'date'])
            ->setDesign(PostDesign::BLOCK);

        $config = $builder->build();

        $this->assertInstanceOf(AppearanceConfigInterface::class, $config);
        $this->assertEquals(3, $config->getNumberOfColumns());
        $this->assertTrue($config->shouldDisplayFeaturedImage());
        $this->assertTrue($config->shouldDisplayReadingTime());
        $this->assertEquals(['category', 'tag'], $config->getTaxonomiesToDisplay());
        $this->assertEquals(['author', 'date'], $config->getPostPropertiesToDisplay());
        $this->assertEquals(PostDesign::BLOCK, $config->getDesign());
    }
}
