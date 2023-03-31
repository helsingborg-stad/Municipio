@extends('templates.master')

@section('hero-top-sidebar')
    @includeIf('partials.hero')
    @includeIf('partials.sidebar', ['id' => 'top-sidebar'])
@stop

@section('helper-navigation')
    @includeWhen($helperNavBeforeContent, 'partials.navigation.helper')
@stop

@section('layout')

    <div class="o-container">
        @if (!$isFrontPage)
            @hasSection('helper-navigation')
                <div class="o-grid o-grid--no-margin u-print-display--none">
                    <div class="o-grid-12">
                        @yield('helper-navigation')
                    </div>
                </div>
            @endif
        @endif

        {!! $hook->innerLoopStart !!}

        @if ($hasBlocks && $post)
            {!! $post->postContentFiltered !!}
        @endif

        {!! $hook->innerLoopEnd !!}

        @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])
        @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid']])
        @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid']])

    </div>

    @if ($placeQuicklinksAfterContent)
        @include('partials.navigation.fixed')
    @endif
    @includeWhen($displaySecondaryQuery && !empty($secondaryQuery->posts), 'partials.secondary', [
        'posts' => $secondaryQuery->posts,
        'postType' => $secondaryPostType,
    ])
@stop
