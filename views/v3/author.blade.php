@extends('templates.master')

@section('sidebar-left')

    @includeIf('partials.sidebar', ['id' => 'left-sidebar'])
    @if (get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option'))
        {!! $navigation['sidebarMenu'] !!}
    @endif

    @includeIf('partials.sidebar', ['id' => 'left-sidebar-bottom'])

@stop

@section('content')

    @includeIf('partials.sidebar', ['id' => 'content-area-top'])

    @include('partials.archive.archive-filters')

    <div class="author-archive">

        @typography([
            "element" => "h2"
        ])
            {{_e('Posts by', 'municipio')}}
            {{municipio_get_author_full_name() ? municipio_get_author_full_name() : get_the_author_meta('nicename') }}
        @endtypography
        
        @if (in_array($template, array('cards', 'compressed', 'list', 'newsitem')))
            @include('partials.post.post-' . $template)
        @else
            @include('partials.post.post-list')
        @endif

    </div>

    @includeIf('partials.sidebar.default', ['id' => 'content-area'])

    @pagination([
        'list' => $paginationList,
        'classList' => ['u-margin__top--4'],
        'current' => isset($_GET['pagination']) ? $_GET['pagination'] : 1
    ])
    @endpagination

@stop

@section('sidebar-right')
    @includeIf('partials.sidebar', ['id' => 'right-sidebar'])
@stop

@section('below')
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom'])
@stop