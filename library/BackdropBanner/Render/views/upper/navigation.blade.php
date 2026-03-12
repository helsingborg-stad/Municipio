@element([
    'classList' => [
        'o-layout-grid',
        'o-layout-grid--cq',
        'backdrop-banner__navigation',
    ],
])
    @element([
        'classList' => ['backdrop-banner__navigation-container']
    ])
        @foreach($rows as $index => $row)
            @element([
                'classList' => ['backdrop-banner__navigation-item']
            ])
                @element([
                    'classList' => [
                        'backdrop-banner__navigation-item-container'
                    ]
                ])
                    @typography([
                        'element' => 'h2',
                        'variant' => 'h1',
                        'classList' => ['backdrop-banner__navigation-item-title'],
                    ])
                        {{ !empty($row['title']) ? $row['title'] : 'Row' . ($index + 1) }}
                    @endtypography
                    @if(!empty($row['description']))
                        @typography([
                            'element' => 'p',
                            'variant' => 'body',
                            'classList' => ['backdrop-banner__navigation-item-description', 'u-margin__top--0'],
                        ])
                            {{ $row['description']}}
                        @endtypography
                    @endif
                @endelement
            @endelement
        @endforeach
    @endelement
@endelement