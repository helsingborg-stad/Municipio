@extends('templates.master')

@section('before-layout')
@stop

@section('above')
    @breadcrumb([
        'list' => \Municipio\Theme\Navigation::breadcrumbData()
    ])
    @endbreadcrumb
@stop

@section('sidebar-left')
    @if (get_field('nav_sub_enable', 'option'))
    {!! $navigation['sidebarMenu'] !!}
    @endif

    @include('partials.sidebar.default', ['id' => 'left-sidebar'])
    @include('partials.sidebar.default', ['id' => 'right-sidebar', 'classes' => 'hidden-lg'])
    @include('partials.sidebar.default', ['id' => 'left-sidebar-bottom'])
@stop

@section('content')

    @includeIf('partials.sidebar.default', ['id' => 'content-area-top'])

    @section('loop')
        @if($post)
            @include('partials.article', (array) $post)
        @endif
    @show

    @includeIf('partials.sidebar.default', ['id' => 'content-area'])

@stop

@section('sidebar-right')
    @includeIf('partials.sidebar.default', ['id' => 'right-sidebar'])
@stop

@section('below')
    @includeIf('partials.sidebar.default', ['id' => 'content-area-bottom'])
@stop
