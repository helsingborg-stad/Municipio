@if ($showFilter)
    <style>
        html {
            scroll-behavior: unset !important;
        }
    </style>

    <div class="s-archive-filter">
        @form([
        'method' => 'GET',
        'action' => '?q=form_component'
        ])

        @if ($enableTextSearch)
            <div class="o-grid">
                <div class="o-grid-12">
                    @field([
                        'type' => 'search',
                        'name' => 's',
                        'value' => $queryParameters->search,
                        'label' => $lang->searchFor,
                        'classList' => ['u-width--100'],
                        'required' => false
                    ])
                    @endfield
                </div>
            </div>
        @endif

        @if ($enableDateFilter)
            <div class="o-grid">
                <div class="o-grid-12@xs o-grid-auto@sm">
                    @field([
                        'type' => 'date',
                        'name' => 'from',
                        'value' => $queryParameters->from,
                        'label' => $lang->fromDate,
                        'attributeList' => [
                            'data-invalid-message' => $lang->dateInvalid,
                            'js-archive-filter-from' => ''
                        ],
                        'required' => false,
                        'datepicker' => [
                            'title' => $lang->fromDate,
                            'minDate' => false,
                            'maxDate' => false,
                            'required' => true,
                            'showResetButton' => true,
                            'showDaysOutOfMonth' => true,
                            'showClearButton' => true,
                            'hideOnBlur' => true,
                            'hideOnSelect' => false
                        ]
                    ])
                    @endfield
                </div>

                <div class="o-grid-12@xs o-grid-auto@sm">
                    @field([
                        'type' => 'date',
                        'name' => 'to',
                        'value' => $queryParameters->to,
                        'label' => $lang->toDate,
                        'attributeList' => [
                            'data-invalid-message' => $lang->dateInvalid,
                            'js-archive-filter-to' => ''
                        ],
                        'required' => false,
                        'datepicker' => [
                            'title' => $lang->toDate,
                            'minDate' => false,
                            'maxDate' => false,
                            'required' => true,
                            'showResetButton' => true,
                            'showDaysOutOfMonth' => true,
                            'showClearButton' => true,
                            'hideOnBlur' => true,
                            'hideOnSelect' => false
                        ]
                    ])
                    @endfield
                </div>
            </div>
        @endif

        {{-- Select dropdowns for filtering --}}
        <div class="o-grid u-align-content--end">
            @foreach ($taxonomyFilters as $key => $select)
                <div class="o-grid-12@xs o-grid-6@sm o-grid-auto@md">
                    @if ('multi' === $select['fieldType'])
                        @filterSelect([
                            'label' => $select['label'] ?? '',
                            'name' => $select['attributeList']['name'] ?? '',
                            'required' => $select['required'] ?? false,
                            'options' => $select['options'] ?? [],
                            'preselected' => $select['preselected'] ?? false,
                            'placeholder' => $select['label'] ?? ''
                        ])
                        @endfilterSelect
                    @else
                        @select($select)
                        @endselect
                    @endif
                </div>
            @endforeach

            <div class="o-grid-fit@xs o-grid-fit@sm o-grid-fit@md u-margin__top--auto">
                @button([
                    'text' => $facettingType ? $lang->filterBtn : $lang->searchBtn,
                    'color' => 'primary',
                    'type' => 'submit',
                    'classList' => ['u-display--block@xs', 'u-width--100@xs'],
                    'icon' => $facettingType ? 'filter_list' : 'search',
                ])
                @endbutton
            </div>

            @if ($showFilterReset && $archiveResetUrl)
                <div class="o-grid-fit@xs o-grid-fit@sm o-grid-fit@md u-margin__top--auto">
                    @button([
                        'href' => $archiveResetUrl,
                        'text' => $facettingType ? $lang->resetFilterBtn : $lang->resetSearchBtn,
                        'type' => 'basic',
                        'classList' => ['u-display--block@xs', 'u-width--100@xs'],
                    ])
                    @endbutton
                </div>
            @endif

        </div>
@endform
</div>
@endif
