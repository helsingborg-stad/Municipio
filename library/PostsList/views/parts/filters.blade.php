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

    @if ($filterConfig->isTextSearchEnabled())
        <div class="o-grid">
            <div class="o-grid-12">
                @field([
                    ...$getTextSearchFieldArguments(),
                    'classList' => ['u-width--100'],
                ])
                @endfield
            </div>
        </div>
    @endif

    @if ($filterConfig->isDateFilterEnabled())
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

    <div class="o-grid u-align-content--end">
        
        @foreach ($getTaxonomyFilterSelectComponentArguments() as $selectArguments)
            <div class="o-grid-12@xs o-grid-6@sm o-grid-auto@md u-level-3">
                @select([...$selectArguments, 'size' => 'md'])@endselect
            </div>
        @endforeach
        
        {{-- @foreach ($filterConfig->getTaxonomiesEnabledForFiltering() as $select)
            <div class="o-grid-12@xs o-grid-6@sm o-grid-auto@md u-level-3">
                @select([
                    'label' => $select['label'] ?? '',
                    'name' => $select['attributeList']['name'] ?? '',
                    'required' => $select['required'] ?? false,
                    'placeholder' => $select['label'] ?? '',
                    'preselected' => $select['preselected'] ?? false,
                    'multiple' => (bool) ('multi' === $select['fieldType']),
                    'options' => $select['options'] ?? [],
                    'size' => 'md'
                ])
                @endselect
            </div>
        @endforeach --}}
    
        {{-- Facetting --}}
        <div class="o-grid-fit@xs o-grid-fit@sm o-grid-fit@md u-margin__top--auto">
            @button([
                ...$getFilterFormSubmitButtonArguments(),
                'color' => 'primary',
                'classList' => ['u-display--block@xs', 'u-width--100@xs'],
            ])
            @endbutton
        </div>

        @if ($filterConfig->showReset() && $filterConfig->getResetUrl())
            <div class="o-grid-fit@xs o-grid-fit@sm o-grid-fit@md u-margin__top--auto">
                @button([
                    ...$getFilterFormResetButtonArguments(),
                    'classList' => ['u-display--block@xs', 'u-width--100@xs'],
                ])
                @endbutton
            </div>
        @endif

    </div>
    
    @endform
</div>
