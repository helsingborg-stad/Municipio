@form([
    'id'        => 'hero-search-form',
    'method'    => 'get',
    'action'    => esc_url( home_url( '/' ) ),
    'classList' => ['c-form--hidden']
])
    @typography([
        "element" => "h1",
        'classList' => [
            'u-color__text--primary'
        ]
    ])
        {{ _e('Search', 'municipio') }} helsingborg.se
    @endtypography

    @field([
        'id' => 'search-form--field',
        'type' => 'text',
        'value' => get_search_query(),
        'attributeList' => [
            'type' => 'search',
            'name' => 's',
            'required' => false,
        ],
        'label' => __('Search', 'municipio')
    ])
    @endfield

    @button([
        'id' => 'search-form--submit',
        'text' => __('Search', 'municipio'),
        'color' => 'primary',
        'type' => 'basic',
        'size' => 'lg',
        'attributeList' => [
            'id' => 'search-form--submit'
        ]
    ])
    @endbutton 
@endform