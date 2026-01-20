@element([
    'componentElement' => 'header',
    'classList' => [
        'o-layout-grid--col-span-12',
        'o-layout-grid',
        'o-layout-grid--cols-12',
        'o-layout-grid--gap-4',
        'o-layout-grid--order-0',
        'u-padding__x--3',
        'u-padding__y--5',
        'u-padding__x--8@md',
        'u-padding__y--8@md',
        'u-padding__x--8@lg',
        'u-padding__y--8@lg',
        'u-padding__x--8@xl',
        'u-padding__y--8@xl',
        'u-rounded--16',
    ],
    'attributeList' => [
        'style' => 'background-color: var(--color-secondary);'
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
            @typography(['element' => 'p', 'variant' => 'subtitle'])
                {{-- TODO: Insert excerpt here --}}
            @endtypography
        @endif
    @endelement
    @element([
        'classList' => [
            'o-layout-grid--justify-end'
        ]
    ])
        @if(!empty($currentOccasion))
            @datebadge([ 'date' => $currentOccasion->getStartDate() ]) @enddatebadge
        @endif
    @endelement
@endelement