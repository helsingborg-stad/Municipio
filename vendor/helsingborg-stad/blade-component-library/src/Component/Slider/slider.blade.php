<div id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>
    <div class="{{$baseClass}}--inner" js-slider-inner>
        {{ $slot }}
    </div>

    @button([
        'type' => 'basic',
        'icon' => 'chevron_left',
        'size' => 'md',
        'color' => 'primary',
        'attributeList' => ['js-slider-btn' => 'prev'],
        'classList' => [
            'c-slider__button',
            'c-slider__button--prev'
        ]
    ])
    @endbutton

    @button([
        'type' => 'basic',
        'icon' => 'chevron_right',
        'size' => 'md',
        'color' => 'primary',
        'attributeList' => ['js-slider-btn' => 'next'],
        'classList' => [
            'c-slider__button',
            'c-slider__button--next'
        ]
    ])
    @endbutton

    @if ($showStepper)
        @steppers(['type' => 'dots'])
        @endsteppers
    @endif
</div>