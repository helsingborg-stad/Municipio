<?php

class ImagesTest extends WP_UnitTestCase
{

    public function testClassIsDefined()
    {
        $this->assertTrue(class_exists(\Municipio\Content\Images::class));
    }

    public function testFilterImageNormalizedReturnsFalseIfImageNotNormalized() {
        $markup = '<img src="http://example.com/image.jpg" alt="Image" />';
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $markup);
        $images = $dom->getElementsByTagName('img');
        $sut = new \Municipio\Content\Images();

        $result = $sut->imageHasBeenNormalized(false, $images[0]);
        
        $this->assertFalse($result);
    }
    
    public function testFilterImageNormalizedReturnsTrueIfImageHasAttributeParsed() {
        $markup = '<img src="http://example.com/image.jpg" alt="Image" parsed="1" />';
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $markup);
        $images = $dom->getElementsByTagName('img');
        $sut = new \Municipio\Content\Images();

        $result = $sut->imageHasBeenNormalized(false, $images[0]);
        
        $this->assertTrue($result);
    }
    
    public function testFilterImageNormalizedReturnsTrueIfImageHasComponentClass() {
        $markup = '<img src="http://example.com/image.jpg" alt="Image" class="c-image__image" />';
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $markup);
        $images = $dom->getElementsByTagName('img');
        $sut = new \Municipio\Content\Images();

        $result = $sut->imageHasBeenNormalized(false, $images[0]);
        
        $this->assertTrue($result);
    }
}
