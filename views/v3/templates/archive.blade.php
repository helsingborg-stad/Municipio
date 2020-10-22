@extends('templates.single')

@section('before-layout')
    @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'top')
        <div class="o-container">
            @includeFirst(["partials.archive.archive-" . sanitize_title($postType) . "-filters",
            "partials.archive.archive-filters"])
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
    <div class="archive s-archive s-archive-template-{{sanitize_title($template)}}  s-{{sanitize_title($postType)}}-archive" @if (apply_filters('archive_equal_container', false, $postType, $template))  @endif>
        {!! $hook->loopStart !!}
            


            @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'content')
                @include("partials.archive.archive-filters")
            @endif

            

            @if (!empty($posts))
                @includeIf('partials.post.post-' . $template, ['posts' => $posts])
                
                @pagination([
                    'list' => $paginationList, 
                    'classList' => ['u-margin__top--4'], 
                    'current' => $currentPage,
                    'linkPrefix' => '?paged='
                    ])
                @endpagination
            @else
                @notice([
                    'type' => 'info',
                    'message' => [
                        'text' => $lang['noResult'],
                        'size' => 'md'
                    ]
                ])
                @endnotice
            @endif
        {!! $hook->loopEnd !!}
    </div>
@stop