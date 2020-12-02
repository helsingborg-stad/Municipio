{{-- SEARCH: Form, Field and button component --}}
@modal([
    'id' => 'm-search-modal__trigger', 
    'classList' => ['search-modal'], 
    'size' => 'xl', 
    'overlay' => 'light', 
    'isPanel' => true
])
    @form([
        'method' => 'get',
        'action' => $homeUrl,
        'classList' => ['c-form--hidden']
    ])

        @if($lang->searchQuestion)
            @typography(['variant' => 'h2'])
                {{ $lang->searchQuestion }}
            @endtypography
        @endif

        @group(['direction' => 'horizontal'])

            @field([
                'type' => 'text',
                'value' => $searchQuery,
                'attributeList' => [
                    'type' => 'search',
                    'name' => 's',
                    'required' => false,
                ],
                'label' => $lang->searchOn . " " . $siteName,
                'classList' => ['u-flex-grow--1'],
                'size' => 'lg',
                'radius' => 'xs',
                'icon' => ['icon' => 'search']
            ])
            @endfield

            @button([
                'id' => 'search-form--submit',
                'text' => $lang->search,
                'color' => 'default',
                'type' => 'basic',
                'size' => 'lg',
                'attributeList' => [
                    'id' => 'search-form--submit'
                ]
            ])
            @endbutton

        @endgroup

    @endform

@endmodal