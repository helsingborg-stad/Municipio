@element([
    'classList' => [
        'o-layout-grid',
        'o-layout-grid--cq',
        'backdrop-banner__navigation',
        'o-container',
    ],
    'attributeList' => [
        'data-js-backdrop-banner-navigation' => 'true'
    ]
])
    @element([
        'classList' => ['backdrop-banner__navigation-container'],
    ])
        @foreach($rows as $index => $row)
            @link([
                'componentElement' => 'div',
                'href' => !empty($row['url']) ? $row['url'] : null,
                'classList' => ['backdrop-banner__navigation-item', 'u-no-decoration'],
                'attributeList' => [
                    'data-js-backdrop-banner-navigation-item' => $row['id'],
                    'data-js-backdrop-banner-image-url' => $row['imageUrl'] ?? '',
                ]
            ])
                @element([
                    'componentElement' => 'div',
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
            @endlink
        @endforeach
    @endelement
@endelement