{{-- SEARCH: Form, Field and button component --}}
@modal([
    'id' => 'm-search-modal__trigger', 
    'classList' => ['search-modal'], 
    'size' => 'xl', 
    'overlay' => 'dark', 
    'isPanel' => true
])
    @form([
        'method' => 'get',
        'action' => esc_url( home_url( '/' ) ),
        'classList' => ['c-form--hidden']
    ])
        @field([
            'type' => 'text',
            'value' => get_search_query(),
            'attributeList' => [
                'type' => 'search',
                'name' => 's',
                'required' => false,
            ],
            'label' => _x( 'Search', 'label' )
        ])
        @endfield

     
    @endform

@endmodal