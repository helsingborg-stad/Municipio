@slider(['repeatSlide' => true, 'autoSlide' => false,'padding' => 11, 'showStepper' => false])
    @if(!empty($sliderItems['imageItems']))
        @foreach($sliderItems['imageItems'] as $item)
            @slider__item($item)
            @endslider__item
        @endforeach
    @endif
    @if(!empty($sliderItems['videoItems']))
        @foreach($sliderItems['videoItems'] as $item)
            @slider__item()
                {!! $item['embed'] !!}
            @endslider__item
        @endforeach
    @endif
@endslider