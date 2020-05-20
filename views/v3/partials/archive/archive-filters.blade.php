@form([
    'method' => 'GET',
    'action' => '?q=form_component'
    ])

    @grid(['container' => true, 'columns' => "2", 'col_gap' => '1'])
    @grid([]) 
        @field([
            'type' => 'text',
            'value' => $queryParameters->search,
            'label' => 'Search for '. $postType,
            'classList' => ['u-width--100','u-margin__top--4', 'u-margin__bottom--4', 'u-display--inline-block'],
            'attributeList' => [
                'type' => 'text',
                'name' => 's'
            ],
            'required' => false,
            
            ])
        @endfield
    @endgrid

        @grid(['classList' => ['u-display--inline-flex', 'u-align-items--center']])
            @button([
                'text' => 'Filters',
                'icon' => 'filter_list',
                'toggle' => true,
                'size' => 'lg',
                'attributeList' => ['js-toggle-trigger' => 'filterDiv']
            ])
            @endbutton
        @endgrid
    @endgrid
@endform
    
<div class="u-display--none" js-toggle-item="filterDiv" js-toggle-class="u-display--none">
    @grid(['container' => true, 'columns' => "2", 'col_gap' => '4'])
        @grid([])
            @field([
                'type' => 'datepicker',
                'value' => '',
                'label' => 'Enter a date',
                'attributeList' => [
                    'type' => 'text',
                    'name' => 'text',
                    'data-invalid-message' => "You need to add a valid date!",
                    'js-archive-filter-from'
                ],
                'required' => true,
                'datepicker' => [
                    'title'                 => 'Välj ett datum',
                    'minDate'               => "6/29/1997",
                    'maxDate'               => "tomorrow",
                    'required'              => true,
                    'showResetButton'       => true,
                    'showDaysOutOfMonth'    => true,
                    'showClearButton'       => true,
                    'hideOnBlur'            => true,
                    'hideOnSelect'          => false,
                ]
            ])
            @endfield
        @endgrid

        @grid([])
            @field([
                'type' => 'datepicker',
                'value' => '',
                'label' => 'Enter a date',
                'attributeList' => [
                    'type' => 'text',
                    'name' => 'text',
                    'data-invalid-message' => "You need to add a valid date!",
                    'js-archive-filter-to' => ''
                ],
                'required' => true,
                'datepicker' => [
                    'title'                 => 'Välj ett datum',
                    'minDate'               => "6/29/1997",
                    'maxDate'               => "tomorrow",
                    'required'              => true,
                    'showResetButton'       => true,
                    'showDaysOutOfMonth'    => true,
                    'showClearButton'       => true,
                    'hideOnBlur'            => true,
                    'hideOnSelect'          => false,
                    ]
                    ])
            @endfield
        @endgrid
    @endgrid

    @grid(['container' => true, 'columns' => 'auto-fit', 'col_gap' => 2])
    @foreach($taxonomies as $taxonomy => $terms)
        
        @grid([])
        @splitbutton([
            'items' => $terms['categories'],
            'buttonText' => $terms['currentSlug'],
            'icon' => 'expand_more',
            'dropdownDirection' => 'down',
            'classList' => ['u-margin__bottom--4', 'u-margin__right--4']
        ])
        @endsplitbutton
        @endgrid

    @endforeach
    @endgrid
</div>