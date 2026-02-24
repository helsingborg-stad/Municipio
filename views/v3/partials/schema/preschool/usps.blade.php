@paper(['classList' => ['u-color__bg--complementary-lighter', 'u-padding--2']])
    @typography(['element' => 'h2', 'variant' => 'h2', 'classList' => ['u-margin__bottom--2']])
        {!! $lang->uspsLabel !!}
    @endtypography

    @element([
        'componentElement' => 'ul',
        'classList' => [
            'o-layout-grid',
            'o-layout-grid--cq',
            'o-layout-grid--cols-3@md'
        ]
    ])
        @foreach ($usps as $uspColumn)
            @foreach ($uspColumn as $uspItem)
                @element([
                    'componentElement' => 'li',
                    'classList' => ['u-margin__top--1']
                ])
                    {!! $uspItem !!}
                @endelement
            @endforeach
        @endforeach
    @endelement
@endpaper
