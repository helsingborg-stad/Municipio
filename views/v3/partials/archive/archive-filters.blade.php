

@form([
    'method' => 'GET',
    'action' => '?q=form_component'
    ])

    <div class="o-grid">

        <div class="o-grid-6">
            @field(
                [
                    'type' => 'text',
                    'value' => $queryParameters->search,
                    'label' => 'Search for '. $postType,
                    'classList' => ['u-width--100'],
                    'attributeList' => [
                        'type' => 'text',
                        'name' => 's'
                    ],
                    'required' => false,
                ]
            )
            @endfield
        </div>

        <div class="o-grid-2">
            @button([
                'icon' => 'date_range',
                'toggle' => true,
                'attributeList' => ['js-toggle-trigger' => 'dateWrapper'],
                'style' => 'basic',
            ])
            @endbutton
        </div>
    
    </div>
        
    <div class="{{ $displayDatePickers ? '' : 'u-display--none' }}" js-toggle-item="dateWrapper" js-toggle-class="u-display--none">
        <div class="o-grid">
            <div class="o-grid-6">
                @field([
                    'type' => 'datepicker',
                    'value' => $queryParameters->from,
                    'label' => __("Choose a from date", 'municipio'),
                    'attributeList' => [
                        'type' => 'text',
                        'name' => 'from',
                        'data-invalid-message' => __("You need to add a valid date.", 'municipio'),
                        'js-archive-filter-from'
                    ],
                    'required' => false,
                    'datepicker' => [
                        'title'                 => __("Choose a from date", 'municipio'),
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
            </div>
            <div class="o-grid-6">
                @field([
                    'type' => 'datepicker',
                    'value' => $queryParameters->to,
                    'label' => __("Choose a to date", 'municipio'),
                    'attributeList' => [
                        'type' => 'text',
                        'name' => 'to',
                        'data-invalid-message' => __("You need to add a valid date.", 'municipio'),
                        'js-archive-filter-to' => ''
                    ],
                    'required' => false,
                    'datepicker' => [
                        'title'                 => __("Choose a to date", 'municipio'),
                        'minDate'               => "6/29/1997",
                        'maxDate'               => "tomorrow",
                        'required'              => true,
                        'showResetButton'       => true,
                        'showDaysOutOfMonth'    => true,
                        'showClearButton'       => true,
                        'hideOnBlur'            => true,
                        'hideOnSelect'          => false,
                        ]
                    ]
                )
                @endfield
            </div>
        </div>
    </div>

    {{-- Select dropdowns for filtering --}}
    <div class="o-grid">
        @foreach($taxonomySelects as $key => $select)
            <div class="o-grid-4">
                @select($select)
                @endselect
            </div>
        @endforeach
    </div>

    <div class="o-grid">
        <div class="o-grid-4">
            
            @button([
                'text' => __("Submit", 'municipio'),
                'color' => 'primary',
                'type' => 'basic'
            ])
            @endbutton

            @if($showFilterReset && $archiveBaseUrl) 
                @button([
                    'href' => $archiveBaseUrl,
                    'text' => __("Reset", 'municipio'),
                    'type' => 'basic'
                ])
                @endbutton
            @endif
      </div>
    </div>
@endform