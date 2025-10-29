@element([
    'classList' => [
        'o-layout-grid--col-span-12',
        'o-layout-grid',
        'o-layout-grid--gap-6',
        'o-layout-grid--order-23',
        'o-layout-grid--cols-12',
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
        'style' => 'color: var(--color-secondary-contrasting); background-color: color-mix(in srgb, var(--color-secondary), transparent 70%)'
    ]
])
    @typography([
        'element' => 'h2',
        'variant' => 'h2',
        'classList' => [
            'o-layout-grid--col-span-12'
        ]
    ])
        {!! $lang->relatedEventsTitle !!}
    @endtypography
    @foreach($relatedPosts as $relatedPost)
        @segment([
            'layout'            => 'card',
            'image'             => $relatedPost->getImage(),
            'link'              => $relatedPost->getPermalink(),
            'title'             => $relatedPost->getTitle(),
            'classList' => [
                'o-layout-grid--col-span-12',
                'o-layout-grid--col-span-6@md',
                'o-layout-grid--col-span-4@lg'
            ]
        ])
            @slot('floating')
                @datebadge([
                    'date' => $relatedPost->getArchiveDateTimestamp(),
                    'size' => 'md',
                    'translucent' => true
                ]) 
                @enddatebadge
            @endslot
        @endsegment
    @endforeach
@endelement