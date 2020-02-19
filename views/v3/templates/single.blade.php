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
    @include('components.dynamic-sidebar', ['id' => 'right-sidebar', 'classes' => 'hidden-lg'])
    @include('components.dynamic-sidebar', ['id' => 'left-sidebar-bottom'])
@stop

@section('content')
    @include('components.dynamic-sidebar', ['id' => 'content-area-top'])

    @while(have_posts())
        {!! the_post() !!}
        @section('loop')
            @include('partials.article', $post)
        @show
    @endwhile

    @include('components.dynamic-sidebar', ['id' => 'content-area'])

    <div class="hidden-xs hidden-sm">
        @include('partials.page-footer')
    </div>
@stop

@section('sidebar-right')
    @include('components.dynamic-sidebar', ['id' => 'right-sidebar'])
@stop

@section('below')
    <div class="hidden-md hidden-lg">
        @include('partials.page-footer')
    </div>
    @include('components.dynamic-sidebar', ['id' => 'content-area-bottom'])
@stop
