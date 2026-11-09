<?php

declare(strict_types=1);

namespace Modularity\Module\Slider;

use AcfService\Implementations\FakeAcfService;
use Modularity\Helper\AcfService;
use Modularity\Helper\WpService;
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
        static::assertIsArray($slider->data());
    }

    #[TestDox('data() uses current slider component defaults')]
    public function testDataUsesCurrentSliderComponentDefaults(): void
    {
        $data = (new Slider())->data();

        static::assertSame(1, $data['slidesPerPage']);
        static::assertFalse($data['peekSlides']);
        static::assertFalse($data['slideByPage']);
        static::assertTrue($data['navigationHover']);
        static::assertTrue($data['compactNavigation']);
    }

    #[TestDox('data() maps supported slider component settings')]
    public function testDataMapsSupportedSliderComponentSettings(): void
    {
        AcfService::set(new FakeAcfService([
            'getFields' => [
                'slides_autoslide' => 1,
                'slides_slide_timeout' => 8,
                'slider_format' => 'ratio-16-9',
                'slider_preamble' => 'A supporting introduction.',
                'slides_per_page' => 4,
                'slider_peek_slides' => 1,
                'slider_slide_by_page' => 1,
                'slider_navigation_hover' => 0,
                'slider_compact_navigation' => 0,
            ],
        ]));

        $data = (new Slider())->data();

        static::assertSame(8, $data['autoslide']);
        static::assertSame('A supporting introduction.', $data['preamble']);
        static::assertSame(4, $data['slidesPerPage']);
        static::assertTrue($data['peekSlides']);
        static::assertTrue($data['slideByPage']);
        static::assertFalse($data['navigationHover']);
        static::assertFalse($data['compactNavigation']);
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
        static::assertIsArray($result);
    }
}
