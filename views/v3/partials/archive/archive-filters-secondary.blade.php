@if ($showFilter && !empty($enabledFilters))
    <!-- Makes filtering a more pleasant expreience by keeping scrollstate -->
    <script>
        const scrollStateUrl = new URL(document.referrer);
        if (scrollStateUrl.pathname == window.location.pathname) {
            document.addEventListener("DOMContentLoaded", function(event) {
                var scrollpos = localStorage.getItem('municipioScrollState-{{ $secondaryPostType }}');
                if (scrollpos) {
                    window.scrollTo(0, scrollpos);
                }
            });
        }
        window.onbeforeunload = function(e) {
            localStorage.setItem('municipioScrollState-{{ $secondaryPostType }}', window.scrollY || window.pageYOffset);
        };
    </script>
    <style>
        html {
            scroll-behavior: unset !important;
        }
    </style>

    <div class="s-archive-filter o-grid u-position--relative u-level-top">
        @form([
        'method' => 'GET',
        'action' => '?q=form_component'
        ])
        <div class="o-grid u-align-content--end">
            @foreach ($enabledFilters as $filterType)
                <div class="o-grid-12@xs o-grid-6@sm o-grid-auto@md">
                    @filterSelect([
                        'label' => $filterType['label'],
                        'name' => $filterType['attributeList']['name'] ?? '',
                        'required' => $filterType['required'],
                        'options' => $filterType['options'],
                        'preselected' => $filterType['preselected']
                    ])
                    @endfilterSelect
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
