@if(array_filter($facts, fn($fact) => $fact['value'] !== ''))
    @if($heading)
        <section class="c-stack u-margin__top--5">
            @typography(['element' => 'h2', 'variant' => 'h3', 'classList' => ['u-margin__bottom--0']]){{ $heading }}@endtypography
    @endif
    @collection(['unbox' => true, 'bordered' => true])
        @foreach($facts as $fact)
            @if($fact['value'] !== '')
                @collection__item(['icon' => $fact['icon'], 'classList' => ['u-align-items--center']])
                    @typography()
                        <strong>{{ $fact['label'] }}:</strong> {{ $fact['value'] }}
                    @endtypography
                @endcollection__item
            @endif
        @endforeach
    @endcollection
    @if($heading) </section> @endif
@endif
