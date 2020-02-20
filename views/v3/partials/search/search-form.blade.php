{{-- SEARCH: Form, Field and button component --}}
@form([
    'method' => 'get',
    'action' => esc_url( home_url( '/' ) )
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

@endform
