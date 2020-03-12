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

    @include('partials.sidebar', ['id' => 'left-sidebar'])
    @include('partials.sidebar', ['id' => 'right-sidebar', 'classes' => 'hidden-lg'])
    @include('partials.sidebar', ['id' => 'left-sidebar-bottom'])
@stop

@section('content')

    @includeIf('partials.sidebar', ['id' => 'content-area-top'])

    @section('loop')
        @if($post)
            @include('partials.article', (array) $post)

            @avatar(['image' => $authorAvatar])
            @endavatar

            @typography(['variant' => 'h4', 'element' => 'meta'])
                {{$publishTranslations['by']}} {{$authorName}}
            @endtypography

            @typography(['variant' => 'meta'])
                {{$publishTranslations['published']}} {{$publishedDate}}
            @endtypography

            @typography(['variant' => 'meta'])
                {{$publishTranslations['updated']}} {{$updatedDate}}
            @endtypography
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
