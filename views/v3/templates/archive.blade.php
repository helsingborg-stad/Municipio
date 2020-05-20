@extends('templates.master')

@section('before-layout')

    @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'top')
        @includeFirst(["partials.archive.archive-" . sanitize_title($postType) . "-filters",
        "partials.archive.archive-filters"])
    @endif

@stop

@section('above')
    @breadcrumb([
        'list' => \Municipio\Theme\Navigation::breadcrumbData()
    ])
    @endbreadcrumb
@stop

@section('sidebar-left')

    @includeIf('partials.sidebar', ['id' => 'left-sidebar'])
    @if (get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option'))
        {!! $navigation['sidebarMenu'] !!}
    @endif

    @includeIf('partials.sidebar', ['id' => 'left-sidebar-bottom'])

@stop

@section('content')
    
   
        
    
    @includeIf('partials.sidebar', ['id' => 'content-area-top'])

    @include('partials.archive.archive-title')

    @if (!empty($posts))
  
        <div
            class="archive s-archive s-archive-template-{{sanitize_title($template)}}  s-{{sanitize_title($postType)}}-archive grid"
            @if (apply_filters('archive_equal_container', false, $postType, $template))  @endif>

            @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'content')
                @include("partials.archive.archive-filters")
            @endif
                        
            @includeIf('partials.post.post-' . $template, ['posts' => $posts])
        </div>

        @pagination([
            'list' => $paginationList, 
            'classList' => ['u-margin__top--4'], 
            'current' => isset($_GET['pagination']) ? $_GET['pagination'] : 1
        ])
        @endpagination
    @else
        {{-- TODO: add a notice component --}}
        <?php _e('No posts to show', 'municipio'); ?>
    @endif


    @includeIf('partials.sidebar.default', ['id' => 'content-area'])

@stop


@section('sidebar-right')
    @includeIf('partials.sidebar', ['id' => 'right-sidebar'])
@stop


@section('below')
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom'])
@stop
