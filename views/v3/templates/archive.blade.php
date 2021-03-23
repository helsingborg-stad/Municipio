@extends('templates.single')


@section('sidebar-left')
    @includeIf('partials.navigation.sidebar', ['menuItems' => $secondaryMenuItems])
    @include('partials.sidebar', ['id' => 'left-sidebar', 'classes' => ['o-grid']])
    @include('partials.sidebar', ['id' => 'left-sidebar-bottom', 'classes' => ['o-grid']])
@stop

@section('before-layout')
    @if ($filterPosition == 'top')
        <div class="o-container">
            @includeFirst([
                "partials.archive.archive-" . sanitize_title($postType) . "-filters",
                "partials.archive.archive-filters"
            ])
        </div>
    @endif
@stop

@section('sidebar-left')

    @sidebar([
        'items' => $secondaryMenuItems,
        'endpoints' => [
            'children' => $homeUrlPath . '/wp-json/municipio/v1/navigation/children'
        ],
        'pageId' => $pageID,
        'sidebar' => true
    ])
    @endsidebar

    @includeIf('partials.sidebar', ['id' => 'left-sidebar', 'classes' => ['o-grid']])
    @includeIf('partials.sidebar', ['id' => 'left-sidebar-bottom', 'classes' => ['o-grid']])
@stop

@section('content')
    <div class="archive s-archive s-archive-template-{{sanitize_title($template)}}  s-{{sanitize_title($postType)}}-archive">
        {!! $hook->loopStart !!}
            
            @if($archiveTitle)
                @typography([
                    "variant" => "h1",
                    "element" => "h1",
                    "classList" => ['t-archive-title', 't-' . $postType . '-archive-title', 'u-margin__bottom--2']
                ])
                    {{ $archiveTitle }}
                @endtypography
            @endif

            @if ($filterPosition == 'content')
                @include("partials.archive.archive-filters")
            @endif

            @if (!empty($posts))

                @includeIf('partials.post.post-' . $template, ['posts' => $posts])
                
                @if($showPagination)
                    @pagination([
                        'list' => $paginationList, 
                        'classList' => ['u-margin__top--4', 'u-display--flex', 'u-justify-content--center'], 
                        'current' => $currentPage,
                        'linkPrefix' => '?paged='
                    ])
                    @endpagination
                @endif

            @else

                @notice([
                    'type' => 'info',
                    'message' => [
                        'text' => $lang->noResult,
                        'size' => 'md'
                    ]
                ])
                @endnotice

            @endif
        {!! $hook->loopEnd !!}
    </div>
@stop