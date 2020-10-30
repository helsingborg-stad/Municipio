@form([
    'id'        => 'hero-search-form',
    'method'    => 'get',
    'action'    => $homeUrl,
    'classList' => ['c-form--hidden']
])
    <div class="o-grid">
        <div class="o-grid-auto">
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
        </div>
        <div class="o-grid-fit">
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
        </div>
    </div>
@endform
