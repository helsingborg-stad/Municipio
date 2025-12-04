<?php

declare(strict_types=1);

namespace Modularity\Module\Slider;

use AcfService\Implementations\FakeAcfService;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Modularity\Helper\AcfService;
use Modularity\Helper\WpService;
use Modularity\Integrations\Component\ImageFocusResolver;
use Modularity\Integrations\Component\ImageResolver;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class SliderTest extends TestCase
{
    protected function setUp(): void
    {
        WpService::set(new FakeWpService([
            'addAction' => true,
        ]));

        AcfService::set(new FakeAcfService([
            'getFields' => ['slides_autoslide' => 0, 'slider_format' => 'ratio-16-9'],
        ]));
    }

    #[TestDox('data() returns an array')]
    public function testDataReturnsArray(): void
    {
        $slider = new Slider();
        $this->assertIsArray($slider->data());
    }

    #[TestDox('prepareSlide() handles image being a scalar value of type integer')]
    public function testPrepareSlideHandlesScalarImage(): void
    {
        $slider = new Slider();
        $slide = [
            'acf_fc_layout' => 'image',
            'image' => 123,
        ];
        $imageSize = [1528, false];
        $result = $slider->prepareSlide($slide, $imageSize);
        $this->assertIsArray($result);
    }
}
