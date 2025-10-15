<?php

namespace Modularity\Module\Slider;

use PHPUnit\Framework\TestCase;

class SliderTest extends TestCase {
    
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated() {
        $slider = new Slider();
        $this->assertInstanceOf(Slider::class, $slider);
    }

    /**
     * @testdox slideHasLink returns false if link_type is not internal or external
     */
    public function testSlideHasLinkReturnFalseIfLinkTypeIndicatesNoLink() {
        $slider = new Slider();
        $linkUrl = 'https://example.com';
        $this->assertFalse($slider->slideHasLink([ 'link_type' => 'false', 'link_url' => $linkUrl ]));
    }

    /**
     * @testdox slideHasLink returns false if link_url is empty
     */
    public function testSlideHasLinkReturnFalseIfLinkUrlIsEmpty() {
        $slider = new Slider();
        $this->assertFalse($slider->slideHasLink([ 'link_type' => 'internal', 'link_url' => '' ]));
    }

    /**
     * @testdox slideHasLink returns true if link_type is internal and link_url is not empty
     */
    public function testSlideHasLinkReturnTrueIfLinkTypeIsInternalAndLinkUrlIsNotEmpty() {
        $slider = new Slider();
        $linkUrl = 'https://example.com';
        $this->assertTrue($slider->slideHasLink([ 'link_type' => 'internal', 'link_url' => $linkUrl ]));
    }
}