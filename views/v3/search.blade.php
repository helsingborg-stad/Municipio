@extends('templates.master')
@section('layout')

    <section>

        @form([
            'method' => 'get',
            'action' => $homeUrl,
            'classList' => ['u-margin__bottom--4']
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
        @endform

        <div class="search-result-count">
            @typography(['variant' => 'h2', 'element' => 'h2'])
                {{ $lang['found'] }} {{ $resultCount }} {{ $lang['results'] }} 
            @endtypography
        </div>

    </section>

    {!! $hook->searchNotices !!}

    @if (!$resultCount)
        @notice([
            'type' => 'info',
            'message' => [
                'text' => $lang['noResult'],
                'size' => 'sm'
            ],
            'icon' => [
                'name' => 'info',
                'size' => 'md',
                'color' => 'white'
            ]
        ])
        @endnotice
    @else

        {!! $hook->loopStart !!}

        @foreach($posts as $post) 
            @card([
                'heading' => $post->postTitleFiltered,
                'content' => $post->postExcerpt,
            ])
            @endcard
        @endforeach

        {!! $hook->loopEnd !!}

        <section class="u-mt-0 u-margin__top--2">

            @if ($paginationList)
                @pagination([
                    'list' => $paginationList,
                    'current' => $currentPagePagination
                ])
                @endpagination
            @endif

        </section>

    @endif

@stop