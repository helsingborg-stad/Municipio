@if ($showFilter && !empty($enabledFilters))
    <div class="s-archive-filter o-grid u-position--relative u-level-4">
        @form([
        'method' => 'GET',
        'id' => 'filter',
        'action' => "#filter"
        ])
        <div class="o-grid u-align-content--end">
            @foreach ($enabledFilters as $filter)
                <div class="o-grid-12@xs o-grid-6@sm o-grid-auto@md u-level-3">
                    @select([
                        'label' => $filter['label'] ?? '',
                        'required' => $filter['required'] ?? false,
                        'placeholder' => $filter['label'] ?? '',
                        'preselected' => $filter['preselected'] ?? false,
                        'multiple' => (bool) ('multi' === $filter['fieldType']),
                        'options' => $filter['options'] ?? [],
                        'size' => 'md'
                    ])
                    @endselect
                </div>
            @endforeach

            <div class="o-grid-fit@xs o-grid-fit@sm o-grid-fit@md u-margin__top--auto">
                @button([
                    'text' => $lang->filterBtn,
                    'color' => 'primary',
                    'type' => 'submit',
                    'classList' => ['u-display--block@xs', 'u-width--100@xs'],
                    'icon' => 'filter_list',
                ])
                @endbutton
            </div>

            @if ($showFilterReset && $archiveResetUrl)
                <div class="o-grid-fit@xs o-grid-fit@sm o-grid-fit@md u-margin__top--auto">
                    @button([
                        'href' => $archiveResetUrl,
                        'text' => $lang->resetFilterBtn,
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
