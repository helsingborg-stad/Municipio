@element([
    'classList' => [
        'interactive-map__marker-info-container'
    ],
    'attributeList' => [
        'data-js-interactive-map-marker-info-container' => ''
    ]
])
    @collection__item([
        'classList' => [
            'interactive-map__marker-info'
        ],
        'attributeList' => [
            'data-js-interactive-map-marker-info' => ''
        ]
    ])
        @icon([
            'icon' => 'close',
            'size' => 'md',
            'classList' => [
                'interactive-map__marker-info-close-icon'
            ],
            'attributeList' => [
                'data-js-interactive-map-marker-info-close-icon' => '',
                'role' => 'button',
                'aria-label' => $lang['closeMarker']
            ]
        ])
        @endicon
        @slot('before')
            @element([
                'classList' => [
                    'interactive-map__marker-info-image'
                ],
                'attributeList' => [
                    'data-js-interactive-map-marker-info-image' => ''
                ]
            ])
                <!-- Image placeholder -->
            @endelement
        @endslot
        @group([
            'direction' => 'vertical',
            'classList' => [
                'interactive-map__marker-content'
            ],
        ])
            @typography([
                'element' => 'h2',
                'variant' => 'h3',
                'classList' => [
                    'interactive-map__marker-title'
                ],
                'attributeList' => [
                    'data-js-interactive-map-marker-info-title' => ''
                ]
            ])
            @endtypography
            @typography([
                'classList' => [
                    'interactive-map__marker-description'
                ],
                'attributeList' => [
                    'data-js-interactive-map-marker-info-description' => ''
                ]
            ])
            @endtypography
        @endgroup
    @endcollection__item
@endelement