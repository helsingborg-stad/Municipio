@extends('templates.master')

@section('before-layout')
@stop

@section('above')
    @include('components.breadcrumbs')
@stop

@section('sidebar-left')
    @include('components.dynamic-sidebar', ['id' => 'left-sidebar'])

    @if (get_field('nav_sub_enable', 'option') && isset($layout['sidebarLeft']) && $layout['sidebarLeft'] == true)
    {!! $navigation['sidebarMenu'] !!}
    @endif

    @include('components.dynamic-sidebar', ['id' => 'left-sidebar-bottom'])
@stop

@section('content')
    @include('components.dynamic-sidebar', ['id' => 'content-area-top'])

    @while(have_posts())
        {!! the_post() !!}

        @include('partials.article')
    @endwhile

    @include('components.dynamic-sidebar', ['id' => 'content-area'])
@stop

@section('sidebar-right')
    @include('components.dynamic-sidebar', ['id' => 'right-sidebar'])
@stop

@section('below')
@stop
