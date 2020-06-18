@extends('templates.master')

@section('before-layout')
@stop

@section('above')
    <div class="nav-helper">
        @breadcrumb([
            'list' => \Municipio\Theme\Navigation::breadcrumbData()
        ])
        @endbreadcrumb
        @includeIf('partials.navigation.accessibility')
    </div>
@stop


@section('sidebar-left')
    @if (get_field('nav_sub_enable', 'option'))
        {!! $navigation['sidebarMenu'] !!}
    @endif

    @include('partials.sidebar', ['id' => 'left-sidebar'])
    @include('partials.sidebar', ['id' => 'right-sidebar', 'classes' => 'hidden-lg'])
    @include('partials.sidebar', ['id' => 'left-sidebar-bottom'])
@stop

@section('content')

@includeIf('partials.sidebar', ['id' => 'content-area-top'])

@section('loop')
    @if($post)
        @include('partials.article', (array) $post)
    @endif
@show

@includeIf('partials.sidebar', ['id' => 'content-area'])
@stop

@section('sidebar-right')
    @includeIf('partials.sidebar', ['id' => 'right-sidebar'])
@stop

@section('below')
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom'])
@stop
