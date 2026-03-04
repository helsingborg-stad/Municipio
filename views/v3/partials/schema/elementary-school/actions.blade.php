@paper(['classList' => ['u-color__bg--complementary-lighter', 'u-padding--2']])
    @typography(['element' => 'h2'])
        {!! $actions['title'] ?: $lang->actionsLabel !!}
    @endtypography
    @if(!empty($actions['description']))
        @typography(['element' => 'p', 'classList' => ['u-margin__bottom--3']])
            {!! $actions['description'] !!}
        @endtypography
    @endif
    @element(['classList' => ['o-grid', 'o-grid--half-gutter']])
        @foreach ($actions['buttonsArgs'] as $buttonArgs)
            @element(['classList' => ['o-grid-12@sm', 'o-grid-6@md']])
                @button([...$buttonArgs, 'classList' => ['u-width--100']])@endbutton
            @endelement
        @endforeach
    @endelement
@endpaper