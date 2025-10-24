@element([
    'classList' => [
        'o-layout-grid--col-span-12',
        'o-layout-grid',
        'o-layout-grid--gap-6',
        'o-layout-grid--order-23',
        'o-layout-grid--cols-12',
        'u-padding__x--8',
        'u-padding__y--8',
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
        @card([
            'image' => $relatedPost->getImage(),
            'heading' => $relatedPost->getTitle(),
            'link' => $relatedPost->getPermalink(),
            'dateBadge' => true,
            'date' => [
                'timestamp' => $relatedPost->getArchiveDateTimestamp(),
                'format'    => $relatedPost->getArchiveDateFormat(),
            ],
            'classList' => [
                'o-layout-grid--col-span-12',
                'o-layout-grid--col-span-6@md',
                'o-layout-grid--col-span-4@lg',
            ]
        ])
        @endcard
    @endforeach
@endelement