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
        {{ $lang['search'] }} {{ $siteName }}
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
        'label' => $lang['search'] . " " . $siteName
    ])
    @endfield

    @button([
        'id' => 'search-form--submit',
        'text' => $lang['search'],
        'color' => 'primary',
        'type' => 'basic',
        'size' => 'md',
        'attributeList' => [
            'id' => 'search-form--submit'
        ]
    ])
    @endbutton 
@endform