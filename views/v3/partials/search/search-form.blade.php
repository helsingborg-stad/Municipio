{{-- SEARCH: Form, Field and button component --}}

@button([
    'color' => 'primary',
    'style' => 'basic',
    'icon' => 'search',
    'size' => 'lg',
    'text' => _x( 'Search', 'label' ),
    'classList' => ['c-button--show-search']
])
@endbutton

@form([
    'method' => 'get',
    'action' => esc_url( home_url( '/' ) ),
    'classList' => ['c-form--hidden']
])
    @grid([
        "container" => true,
        "col_gap" => 8,
        "row_gap" => 3
    ])

        @grid([
            "col" => [
            "xs" => [1,10],
            "sm" => [1,10],
            "md" => [1,10],
            "lg" => [1,10],
            "xl" => [1,10]
        ],
            "row" => [
            "xs" => [1,2],
            "sm" => [1,2],
            "md" => [1,2],
            "lg" => [1,2],
            "xl" => [1,2]
        ]
        ])

            @field([
                'type' => 'text',
                'value' => get_search_query(),
                'attributeList' => [
                    'type' => 'search',
                    'name' => 's',
                    'required' => false,
                ],
                'label' => _x( 'Search for:', 'label' )
            ])
            @endfield

        @endgrid

        @grid([
            "col" => [
                "xs" => [1,2],
                "sm" => [1,2],
                "md" => [10,2],
                "lg" => [10,2],
                "xl" => [10,2]
            ],
                "row" => [
                "xs" => [1,2],
                "sm" => [1,2],
                "md" => [1,2],
                "lg" => [1,2],
                "xl" => [1,2]
        ]
        ])
            @button([
                'type' => 'filled',
                'icon' => 'search',
                'size' => 'md',
                'color' => 'secondary',
                'attributeList' => [
                    'type' => 'submit'
                ]
            ])
            @endbutton

        @endgrid

    @endgrid
@endform
