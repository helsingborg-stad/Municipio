<?php var_dump($_GET); ?>
@form([
    'method' => 'GET',
    'action' => '?q=form_component'
    ])

    <div class="o-grid">

        <div class="o-grid-10">
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
                'text' => __("Show more filter options", 'municipio'),
                'icon' => 'filter_list',
                'toggle' => true,
                'attributeList' => ['js-toggle-trigger' => 'filterDiv']
            ])
            @endbutton
        </div>
        

    </div>


    <div class="o-grid-12">
        @button([
            'text' => 'Submit',
            'color' => 'primary',
            'type' => 'basic'
        ])
        @endbutton
    </div>


    
<div class="u-display--none" js-toggle-item="filterDiv" js-toggle-class="u-display--none">
    <div class="o-grid">
        <div class="o-grid-6">
            @field([
                'type' => 'datepicker',
                'value' => '',
                'label' => __("From date", 'municipio'),
                'attributeList' => [
                    'type' => 'text',
                    'name' => 'text',
                    'data-invalid-message' => "The date you have entered is not valid.",
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
        </div>
        <div class="o-grid-6">
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
                ]
            )
            @endfield
        </div>
    </div>

    <div class="o-grid">
        @foreach($taxonomies as $taxonomy => $terms)
            <div class="o-grid-4">
                @select($terms)
                @endselect
            </div>
        @endforeach
    </div>
</div>


@endform