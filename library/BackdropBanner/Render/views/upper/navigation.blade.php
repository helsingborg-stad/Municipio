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
                    'data-js-backdrop-banner-image-focal-x' => $row['focalPointX'] ?? 0.5,
                    'data-js-backdrop-banner-image-focal-y' => $row['focalPointY'] ?? 0.5,
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
                    @element([
                        'classList' => ['backdrop-banner__navigation-item-content'],
                    ])
                        @if(!empty($row['subtitle']))
                            @typography([
                                'element' => 'h3',
                                'variant' => 'h4',
                                'classList' => ['backdrop-banner__navigation-item-subtitle', 'u-margin__top--1'],
                            ])
                                {{ $row['subtitle'] }}
                            @endtypography
                        @endif
                        @if(!empty($row['description']))
                            @typography([
                                'element' => 'p',
                                'variant' => 'body',
                                'classList' => ['backdrop-banner__navigation-item-description', 'u-margin__top--2'],
                            ])
                                {{ $row['description']}}
                            @endtypography
                        @endif
                    @endelement
                @endelement
                @if(!empty($row['url']))
                    @icon([
                        'icon' => 'arrow_circle_right',
                        'size' => 'lg',
                        'color' => 'default',
                        'classList' => ['backdrop-banner__navigation-item-icon'],
                    ])
                    @endicon
                @endif
            @endlink
        @endforeach
    @endelement
@endelement