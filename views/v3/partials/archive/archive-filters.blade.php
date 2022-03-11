
@if($showFilter)

    <!-- Makes filtering a more pleasant expreience by keeping scrollstate -->
    <script>
        const scrollStateUrl = new URL(document.referrer); 
        if(scrollStateUrl.pathname == window.location.pathname) {
            document.addEventListener("DOMContentLoaded", function(event) { 
                var scrollpos = localStorage.getItem('municipioScrollState-{{$postType}}');
                if (scrollpos) { 
                    window.scrollTo(0, scrollpos);
                }
            });
        }
        window.onbeforeunload = function(e) {
            localStorage.setItem('municipioScrollState-{{$postType}}', window.scrollY || window.pageYOffset);
        };
    </script>
    <style>html {scroll-behavior: unset !important;}</style>

    <div class="s-archive-filter">
        @form([
            'method' => 'GET',
            'action' => '?q=form_component'
        ])
               
            @if($enableTextSearch)
                <div class="o-grid">
                    <div class="o-grid-12">
                        @field([
                            'type' => 'text',
                            'value' => $queryParameters->search,
                            'label' => $lang->searchFor,
                            'classList' => ['u-width--100'],
                            'attributeList' => [
                                'type' => 'text',
                                'name' => 's'
                            ],
                            'required' => false,
                        ])
                        @endfield
                    </div>
                </div>
            @endif

            @if($enableDateFilter) 
                <div class="o-grid">
                    <div class="o-grid-12@xs o-grid-auto@sm">
                        @field([
                            'type' => 'datepicker',
                            'value' => $queryParameters->from,
                            'label' => $lang->fromDate,
                            'attributeList' => [
                                'type' => 'text',
                                'name' => 'from',
                                'data-invalid-message' => $lang->dateInvalid,
                                'js-archive-filter-from'
                            ],
                            'required' => false,
                            'datepicker' => [
                                'title'                 => $lang->fromDate,
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

                    <div class="o-grid-12@xs o-grid-auto@sm">
                        @field([
                            'type' => 'datepicker',
                            'value' => $queryParameters->to,
                            'label' => $lang->toDate,
                            'attributeList' => [
                                'type' => 'text',
                                'name' => 'to',
                                'data-invalid-message' => $lang->dateInvalid,
                                'js-archive-filter-to' => ''
                            ],
                            'required' => false,
                            'datepicker' => [
                                'title'                 => $lang->toDate,
                                'minDate'               => false,
                                'maxDate'               => false,
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
            @endif
        
            {{-- Select dropdowns for filtering --}}
            <div class="o-grid u-align-content--end">
                @foreach($taxonomyFilters as $key => $select)
                    <div class="o-grid-12@xs o-grid-6@sm o-grid-auto@md">
                        @select($select)
                        @endselect
                    </div>
                @endforeach

                <div class="o-grid-fit@xs o-grid-fit@sm o-grid-fit@md u-margin__top--auto">
                    @button([
                        'text' => $lang->searchBtn,
                        'color' => 'primary',
                        'type' => 'basic',
                        'classList' => ['u-display--block@xs', 'u-width--100@xs']
                    ])
                    @endbutton
                </div>
            
                @if($showFilterReset && $archiveBaseUrl) 
                    <div class="o-grid-fit@xs o-grid-fit@sm o-grid-fit@md u-margin__top--auto">
                        @button([
                            'href' => $archiveBaseUrl,
                            'text' => $lang->resetBtn,
                            'type' => 'basic',
                            'classList' => ['u-display--block@xs', 'u-width--100@xs']
                        ])
                        @endbutton
                    </div>
                @endif
                
            </div>
        @endform
    </div>
@endif