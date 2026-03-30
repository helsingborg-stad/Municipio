@element([
    'classList' => [
        'o-layout-grid--col-span-1',
        'o-layout-grid',
        'o-layout-grid--cols-12',
        'o-layout-grid--gap-4',
        'o-layout-grid--order-0',
        'u-align-items--center'
    ],
])
    @element([
        'classList' => [
            'o-layout-grid--col-span-11',
        ]
    ])
        @typography(['element' => 'h1', 'variant' => 'h1', 'classList' => ['u-margin__top--0']])
            {!! $post->getTitle() !!}
        @endtypography
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
@endpaper