@modal([
    'id' => 'm-search-modal__trigger', 
    'classList' => ['t-search-modal'], 
    'closeButtonText' => $lang->close,
    'size' => 'xl', 
    'isPanel' => true,
    'transparent' => true
])
    @form([
        'method' => 'get',
        'action' => $homeUrl,
        'classList' => ['t-search-modal__form'],
        'id' => 'modal-search-form'
    ])

            @if($lang->searchQuestion)
                @typography(['variant' => 'h2', 'id' => 'modal__label__m-search-modal__trigger', 'classList' => ['t-search-modal__label']])
                    {{ $lang->searchQuestion }}
                @endtypography
            @endif

            @field([
                'id' => 'modal-search-form__field',
                'type' => 'search',
                'name' => 's',
                'required' => false,
                'value' => $searchQuery,
                'label' => $lang->searchQuestion,
                'hideLabel' => true,
                'placeholder' => $lang->searchOn . " " . $siteName,
                'classList' => ['t-search-modal__field'],
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
                ],
                'classList' => ['t-search-modal__submit']
            ])
            @endbutton

    @endform

@endmodal