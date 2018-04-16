@extends('templates.master')

@section('before-layout')
@stop

@section('above')
    @include('components.breadcrumbs')
@stop

@section('sidebar-left')
    @if (get_field('nav_sub_enable', 'option'))
    {!! $navigation['sidebarMenu'] !!}
    @endif

    @include('components.dynamic-sidebar', ['id' => 'left-sidebar'])
    @include('components.dynamic-sidebar', ['id' => 'left-sidebar-bottom'])
@stop

@section('content')
    @include('components.dynamic-sidebar', ['id' => 'content-area-top'])

    @while(have_posts())
        {!! the_post() !!}

        @include('partials.article')
    @endwhile

    @include('components.dynamic-sidebar', ['id' => 'content-area'])

    @include('partials.page-footer')
@stop

@section('sidebar-right')
    @include('components.dynamic-sidebar', ['id' => 'right-sidebar'])
@stop

@section('below')
    @include('components.dynamic-sidebar', ['id' => 'content-area-bottom'])
@stop
