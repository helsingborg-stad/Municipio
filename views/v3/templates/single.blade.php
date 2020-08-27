@extends('templates.master')

@section('before-layout')
@stop

@section('above')
    <div class="nav-helper">
        @includeIf('partials.navigation.breadcrumb')
        @includeIf('partials.navigation.accessibility')
    </div>
@stop

@sidebar([
    'logo'          => $logotype->standard['url'],
    'items'         => $primaryMenuItems,
    'pageId'        => $pageID,
    'classList'     => [
        'l-docs--sidebar',
        'c-sidebar--fixed',
        'u-visibility--hidden@md',
        'u-visibility--hidden@lg',
        'u-visibility--hidden@xl'
    ],
    'attributeList' => [
        'js-toggle-item'    => 'js-mobile-sidebar',
        'js-toggle-class'   => 'c-sidebar--collapsed'
    ],
    'endpoints'     => [
        'children'          => $homeUrlPath . '/wp-json/municipio/v1/navigation/children',
        'active'            => $homeUrlPath . '/wp-json/municipio/v1/navigation/active'
    ],
])
@endsidebar

@section('sidebar-left')

    @sidebar([
        'items'     => $secondaryMenuItems,
        'endpoints' => [
            'children'  => $homeUrlPath . '/wp-json/municipio/v1/navigation/children',
            'active'    => $homeUrlPath . '/wp-json/municipio/v1/navigation/active'
        ],
        'pageId'    => $pageID,
        'classList' => [
            'u-visibility--hidden@xs',
            'u-visibility--hidden@sm',
        ]
    ])
    @endsidebar

    @include('partials.sidebar', ['id' => 'left-sidebar'])
    @include('partials.sidebar', ['id' => 'left-sidebar-bottom'])
@stop

@section('content')

    @includeIf('partials.sidebar', ['id' => 'content-area-top'])

    @section('loop')
        {!! $hook->loopStart !!}
        @if($post)
            @include('partials.article', (array) $post)
        @endif
        {!! $hook->loopEnd !!}
    @show

    @includeIf('partials.sidebar', ['id' => 'content-area'])

@stop

@section('sidebar-right')
    @includeIf('partials.sidebar', ['id' => 'right-sidebar'])
@stop

@section('below')
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom'])
@stop
