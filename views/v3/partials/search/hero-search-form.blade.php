@form([
    'id'        => 'hero-search-form',
    'method'    => 'get',
    'action'    => $homeUrl,
    'classList' => ['c-form--hidden']
])
    @group(['direction' => 'horizontal'])
        @field([
            'id' => 'search-form--field',
            'type' => 'text',
            'value' => get_search_query(),
            'attributeList' => [
                'type' => 'search',
                'name' => 's',
                'required' => false,
            ],
            'label' => $lang->searchOn . " " . $siteName,
            'classList' => ['u-flex-grow--1']
        ])
        @endfield
        @button([
            'id' => 'search-form--submit',
            'text' => $lang->search,
            'color' => 'primary',
            'type' => 'basic',
            'size' => 'md',
            'attributeList' => [
                'id' => 'search-form--submit'
            ]
        ])
        @endbutton
    @endgroup
@endform
