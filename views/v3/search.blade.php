@extends('templates.master')
@section('layout')

    @if($hook->customSearchPage) 
        <section class="o-container t-customsearchpage u-margin__top--6">
            <div class="o-row">
                <div class="o-col-12">
                    {!! $hook->customSearchPage !!}
                </div>
            </div>
        </section>
    @else 
    
        <section class="o-container t-searchform u-margin__top--6">

            <div class="o-row">
                
                <div class="o-col-12">
                    @form([
                        'method' => 'get',
                        'action' => $homeUrl,
                        'classList' => []
                    ])
                        @field([
                            'type' => 'text',
                            'value' => $keyword,
                            'attributeList' => [
                                'type' => 'search',
                                'name' => 's',
                                'required' => true,
                            ],
                            'label' => $lang['searchFor']
                        ])
                        @endfield

                        @button([
                            'id' => 'search-form--submit',
                            'text' => __('Search', 'municipio'),
                            'color' => 'primary',
                            'type' => 'basic',
                            'size' => 'lg',
                            'attributeList' => [
                                'id' => 'search-form--submit'
                            ]
                        ])
                        @endbutton 

                    @endform

                </div>

                <div class="o-col-12">
                    <div class="search-result-count">
                        @typography(['variant' => 'meta', 'element' => 'span'])
                            {{ $lang['found'] }} {{ $resultCount }} {{ $lang['results'] }} 
                        @endtypography
                    </div>
                </div>

            </div>

        </section>

        {!! $hook->searchNotices !!}


        @if (!$resultCount)

            <section class="o-container t-searchform u-margin__top--6">
                <div class="o-row">
                    <div class="o-col-12">
                        @notice([
                            'type' => 'info',
                            'message' => [
                                'text' => $lang['noResult'],
                                'size' => 'md'
                            ]
                        ])
                        @endnotice
                    </div>
                </div>
            </section>
            
        @else

            <section class="o-container t-searchresult u-margin__top--6">
                <div class="o-row">
                    <div class="o-col-12">

                        {!! $hook->loopStart !!}

                        @foreach($posts as $post) 

                            @card([
                                'heading' => $post->postTitleFiltered,
                                'subHeading' => $siteName,
                                'content' => $post->postExcerpt,
                                'link' => $post->permalink,
                                'classList' => ['u-margin__top--4']
                            ])
                            
                            @typography(["variant" => "meta"])

                                @icon(['icon' => 'link', 'size' => 'inherit'])
                                @endicon

                                {{ $post->permalink }}

                            @endtypography

                            @endcard

                        @endforeach

                        {!! $hook->loopEnd !!}
                        
                    </div>
                </div>
            </section>

            <section class="t-searchpagination u-mt-0 u-margin__top--2">

                @if ($paginationList)
                    @pagination([
                        'list' => $paginationList,
                        'current' => $currentPagePagination,
                        'classList' => ['u-margin__top--8', 'u-margin__left--auto']
                    ])
                    @endpagination
                @endif

            </section>

        @endif

    @endif

@stop