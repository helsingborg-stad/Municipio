@modal([
    'id' => 'm-search-modal__trigger', 
    'classList' => ['search-modal'], 
    'closeButtonText' => $lang->close,
    'size' => 'xl', 
    'isPanel' => true,
    'transparent' => true
])
    @form([
        'method' => 'get',
        'action' => $homeUrl,
        'classList' => ['search-form', 'c-form--hidden'],
        'id' => 'modal-search-form'
    ])

        @if($lang->searchQuestion)
            @typography(['variant' => 'h1', 'id' => 'modal__label__m-search-modal__trigger'])
                {{ $lang->searchQuestion }}
            @endtypography
        @endif

        @group(['direction' => 'horizontal'])

            @field([
                'id' => 'modal-search-form__field',
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
                'icon' => ['icon' => 'search'],
                'attributeList' => ['autofocus' => '1'],
            ])
            @endfield

            @button([
                'id' => 'modal-search-form__submit',
                'text' => $lang->search,
                'color' => 'default',
                'type' => 'submit',
                'size' => 'lg',
                'attributeList' => [
                    'aria-label' => $lang->search,
                ]
            ])
            @endbutton

        @endgroup

    @endform

@endmodal