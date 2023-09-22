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
        @if (!empty($showPageTitleOnOnePage) && !empty($post) && (!empty($post->postTitle) || !empty($post->callToActionItems['floating'])))
            @group([
                'justifyContent' => 'space-between',
                'classList' => [
                    'u-margin__y--4'
                ]
            ])
                @if (!empty($post->postTitle))
                    @typography([
                        'element' => 'h1', 
                        'variant' => 'h1',
                        'id' => 'page-title',
                        'classList' => [
                            'u-margin__bottom--0'
                        ]
                        ])
                        {!! $post->postTitle !!}
                    @endtypography
                @endif
                @if (!empty($post->callToActionItems['floating']))
                    @icon($post->callToActionItems['floating'])
                    @endicon
                @endif
            @endgroup
        @endif

        @if ($hasBlocks && $post)
            {!! $post->postContentFiltered !!}
        @endif

        {!! $hook->innerLoopEnd !!}

        @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])
        @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid']])
        @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid']])

    </div>

    @includeWhen($displayQuicklinksAfterContent, 'partials.navigation.fixed')

    @includeWhen($displaySecondaryQuery, 'partials.secondary', [
        'posts' => $secondaryQuery->posts ?? [],
        'postType' => $secondaryPostType ?? null,
    ])
@stop
