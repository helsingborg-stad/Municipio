@extends('templates.single')
@section('helper-navigation')
    @includeWhen($helperNavBeforeContent, 'partials.navigation.helper')
@stop
@section('sidebar-left')
    @if ($showSidebars)
        @include('partials.navigation.sidebar-wrapper', ['position' => 'left'])
    @endif
    @include('partials.sidebar', [
        'id' => 'left-sidebar-bottom',
        'classes' => ['o-grid'],
    ])
@stop

@section('content')

    @if ($archiveTitle || $archiveLead)
        <article class="c-article c-article--readable-width s-article u-clearfix" id="article">
            @scope(['name' => ['archive-lead', $postType . '-archive-lead']])
                @if ($archiveTitle)
                    @typography([
                        'variant' => 'h1',
                        'element' => 'h1',
                        'classList' => ['t-archive-title', 't-' . $postType . '-archive-title'],
                        'id' => 'page-title'
                    ])
                        {{ $archiveTitle }}
                    @endtypography
                @endif
                @if ($archiveLead)
                    @typography([
                        'element' => 'p',
                        'classList' => ['lead', 't-archive-lead', 't-' . $postType . '-archive-lead']
                    ])
                        {{ $archiveLead }}
                    @endtypography
                @endif
            @endscope
        </article>
    @endif

    @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])
    @scope(['name' => ['archive', $postType . '-archive']])

        {!! $hook->loopStart !!}

        @includeWhen($archiveMenuItems, 'partials.archive.archive-menu')
        
        @section('loop')
            @if ($displayArchiveLoop)
                @scope(['name' => ['archive-list', $postType . '-archive-list']])
                    @include('posts-list')
                @endscope
            @endif
        @show

        @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid']])

        {!! $hook->loopEnd !!}

    @endscope
@stop

@section('sidebar-right')
    @if ($showSidebars)
        @include('partials.navigation.sidebar-wrapper', ['position' => 'right'])
    @endif
    @includeIf('partials.sidebar', [
        'id' => 'right-sidebar',
        'classes' => ['o-grid'],
    ])
@stop
