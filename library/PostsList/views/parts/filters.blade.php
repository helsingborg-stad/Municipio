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
        @element(['classList' => ['o-grid']])
            @element(['classList' => ['o-grid-12@xs', 'o-grid-auto@sm']])
                @field($getDateFilterFieldArguments()['from'])@endfield
            @endelement
            @element(['classList' => ['o-grid-12@xs', 'o-grid-auto@sm']])
                @field($getDateFilterFieldArguments()['to'])@endfield
            @endelement
        @endelement
    @endif

    <div class="o-grid u-align-content--end">
        
        @foreach ($getTaxonomyFilterSelectComponentArguments() as $selectArguments)
            <div class="o-grid-12@xs o-grid-6@sm o-grid-auto@md u-level-3">
                @select([...$selectArguments, 'size' => 'md'])@endselect
            </div>
        @endforeach
    
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
