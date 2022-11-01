{{-- SEARCH: Form, Field and button component --}}
@modal([
    'id' => 'm-search-modal__trigger', 
    'classList' => ['search-modal'], 
    'size' => 'xl', 
    'isPanel' => true,
])
    @form([
        'method' => 'get',
        'action' => $homeUrl,
        'classList' => ['c-form--hidden']
    ])

        @if($lang->searchQuestion)
            @typography(['variant' => 'h1'])
                {{ $lang->searchQuestion }}
            @endtypography
        @endif

        @group(['direction' => 'horizontal'])

            @field([
                'type' => 'search',
                'name' => 's',
                'required' => false,
                'value' => $searchQuery,
                'label' => $lang->searchQuestion,
                'hideLabel' => true,
                'placeholder' => $lang->searchOn . " " . $siteName,
                'classList' => ['u-flex-grow--1'],
                'size' => 'lg',
                'radius' => 'md',
                'icon' => ['icon' => 'search']
            ])
            @endfield

            @button([
                'id' => 'search-form--submit',
                'text' => $lang->search,
                'color' => 'default',
                'type' => 'submit',
                'size' => 'lg',
                'attributeList' => [
                    'id' => 'search-form--submit',
                    'aria-label' => $lang->search,
                ]
            ])
            @endbutton

        @endgroup

    @endform

@endmodal