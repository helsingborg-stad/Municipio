@extends('templates.master')
@section('layout')

    @if($hook->customSearchPage) 
        <section class="o-container t-customsearchpage u-margin__top--6">
            <div class="o-grid">
                <div class="o-grid-12">
                    {!! $hook->customSearchPage !!}
                </div>
            </div>
        </section>
    @else 
    
        <section class="o-container t-searchform u-margin__top--6">

            <div class="o-grid">
                
                <div class="o-grid-12">
                    @form([
                        'method' => 'get',
                        'action' => $homeUrl,
                        'classList' => []
                    ])

                        @group(['direction' => 'horizontal'])
                            @field([
                                'id' => 'search-form--field',
                                'type' => 'text',
                                'attributeList' => [
                                    'type' => 'search',
                                    'name' => 's',
                                    'required' => true,
                                ],
                                'placeholder' => $lang->searchOn . " " . $siteName,
                                'classList' => ['u-flex-grow--1'],
                                'size' => 'lg',
                                'radius' => 'xs',
                                'icon' => ['icon' => 'search']
                            ])
                            @endfield
                            @button([
                                'id' => 'search-form--submit',
                                'text' => $lang->search,
                                'color' => 'primary',
                                'type' => 'filled',
                                'size' => 'lg',
                                'attributeList' => [
                                    'id' => 'search-form--submit'
                                ]
                            ])
                            @endbutton
                        @endgroup
                    @endform

                    <div class="search-result-count u-margin__top--1">
                        @typography(['variant' => 'meta', 'element' => 'span'])
                            {{ $lang->found }} {{ $resultCount }} {{ $lang->results }} 
                        @endtypography
                    </div>
                </div>
            </div>

        </section>

        {!! $hook->searchNotices !!}

        @if (!$resultCount)
            <section class="o-container t-searchform u-margin__top--6">
                <div class="o-grid">
                    <div class="o-grid-12">
                        @notice([
                            'type' => 'info',
                            'message' => [
                                'text' => $lang->noResult,
                                'size' => 'md'
                            ]
                        ])
                        @endnotice
                    </div>
                </div>
            </section>
        @else

            <section class="o-container t-searchresult">
                <div class="o-grid">
                    <div class="o-grid-12">

                        {!! $hook->loopStart !!}

                        @foreach($posts as $post) 
                            @card([
                                'heading' => $post->postTitleFiltered,
                                'subHeading' => $siteName,
                                'content' => $post->excerpt,
                                'link' => $post->permalink,
                                'classList' => ['u-margin__top--4']
                            ])
                            @endcard
                        @endforeach

                        {!! $hook->loopEnd !!}
                        
                    </div>
                </div>
            </section>

            <section class="t-searchpagination u-mt-0 u-margin__top--2 u-margin__bottom--4">

                @if ($showPagination)
                    @pagination([
                        'list' => $paginationList, 
                        'classList' => ['u-margin__top--4', 'u-display--flex', 'u-justify-content--center'], 
                        'current' => $currentPagePagination,
                        'linkPrefix' => ''
                    ])
                    @endpagination
                @endif

            </section>

        @endif

    @endif

@stop