@element([
    'componentElement' => 'header',
    'classList' => [
        'o-layout-grid--col-span-12',
        'o-layout-grid',
        'o-layout-grid--cols-12',
        'o-layout-grid--gap-4',
        'o-layout-grid--order-0',
        'u-padding__x--8',
        'u-padding__y--8',
        'u-rounded--16',
    ],
    'attributeList' => [
        'style' => 'background-color: color-mix(in srgb, var(--color-secondary), transparent 70%)'
    ]
])
    @element([
        'classList' => [
            'o-layout-grid--col-span-11'
        ],
        'attributeList' => [
            'style' => 'max-width: 800px; color: var(--color-secondary-contrasting);'
        ]
    ])
        @typography(['element' => 'h1', 'variant' => 'h1'])
            {!! $post->getTitle() !!}
        @endtypography
        @if(!empty($description))
        {{-- TODO: Fix this to the correct content --}}
            @typography(['element' => 'p', 'variant' => 'subtitle'])
                {{-- {!! $description !!} --}}
                Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum id ligula porta felis euismod semper. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.
            @endtypography
        @endif
    @endelement
    @element([
        'classList' => [
            'o-layout-grid--justify-end'
        ]
    ])
        @datebadge([
            'date' => $currentOccasion->getStartDate(),
            'translucent' => true
        ])
        @enddatebadge
    @endelement
@endelement