@extends('templates.master')

@section('before-layout')
@stop

@section('above')
    <div class="nav-helper">
        @includeIf('partials.navigation.breadcrumb')
        @includeIf('partials.navigation.accessibility')
    </div>
@stop

@section('sidebar-left')

    @sidebar([
        'items'     => $secondaryMenuItems,
        'endpoints' => [

            'children'  => $homeUrlPath . '/wp-json/municipio/v1/navigation/children',
            'active'    => $homeUrlPath . '/wp-json/municipio/v1/navigation/active'
        ],
        'classList' => [
            'u-visibility--hidden@xs',
            'u-visibility--hidden@sm',
        ],
        'pageId' => $pageID,
        'sidebar' => true
    ])
    @endsidebar

    @include('partials.sidebar', ['id' => 'left-sidebar', 'classes' => ['o-row']])
    @include('partials.sidebar', ['id' => 'left-sidebar-bottom', 'classes' => ['o-row']])
@stop

@section('content')

    @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-row']])

    @section('loop')
        {!! $hook->loopStart !!}
        @if($post)
            @include('partials.article', (array) $post)
        @endif
        {!! $hook->loopEnd !!}
    @show

    @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-row']])

@stop

@section('sidebar-right')
    @includeIf('partials.sidebar', ['id' => 'right-sidebar', 'classes' => ['o-row']])
@stop

@section('below')
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-row']])
@stop