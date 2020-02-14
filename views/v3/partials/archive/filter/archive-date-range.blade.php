<div class="grid-sm-12 grid-md-auto">
    <div class="input-group">

        @field([
            'type' => 'datepicker',
            'value' => isset($_GET['from']) && !empty($_GET['from']) ?
            sanitize_text_field($_GET['from']) : '',
            'label' =>  _e('Date published', 'municipio'),
            'id' => 'filter-date-from',
            'name' => 'from',
            'attributeList' => [
                'type' => 'text',
                'name' => 'text',
                'data-invalid-message' => "You need to add a valid date!",
                'readonly' => 'readonly'
            ],
            'required' => false,
            'classList' => [
                'form-control',
                'datepicker-range',
                'datepicker-range-from'
            ],
            'datepicker' => [
                'title'                 => 'Välj ett datum',
                'minDate'               => false,
                'maxDate'               => false,
                'required'              => true,
                'showResetButton'       => true,
                'showDaysOutOfMonth'    => true,
                'showClearButton'       => true,
                'hideOnBlur'            => true,
                'hideOnSelect'          => false,
            ]
        ])
        @endfield

        @field([
            'type' => 'datepicker',
            'value' => isset($_GET["to"]) && !empty($_GET["to"]) ?
            sanitize_text_field($_GET["to"]) : '',
            'label' =>   _e('To date', 'municipio'),
            'id' => 'filter-date-from',
            'name' => 'to',
            'attributeList' => [
                'type' => 'text',
                'name' => 'text',
                'data-invalid-message' => "You need to add a valid date!",
                'readonly' => 'readonly'
            ],
            'required' => false,
            'classList' => [
                'form-control',
                'datepicker-range',
                'datepicker-range-to'
            ],
            'datepicker' => [
                'title'                 => 'Välj ett datum',
                'minDate'               => false,
                'maxDate'               => false,
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
</div>
