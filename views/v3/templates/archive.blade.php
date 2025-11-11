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
        </article>
    @endif

    @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])

    <div
        class="archive s-archive s-archive-template-{{ sanitize_title($template) }}  s-{{ sanitize_title($postType) }}-archive">

        {!! $hook->loopStart !!}

        @includeWhen($archiveMenuItems, 'partials.archive.archive-menu')

        @if (!empty($posts))
            @if (isset($displayOpenstreetmap) && $displayOpenstreetmap && !empty($pins))
                @openStreetMap([
                    'pins' => $pins,
                    'classList' => ['u-margin__bottom--2'],
                    'containerAware' => true
                ])
                    @if ($postsWithLocation)
                        @slot('sidebarContent')
                            @includefirst(
                                [
                                    'partials.post.' . $postType . '-' . $template,
                                    'partials.post.post-' . $template,
                                ],
                                ['posts' => $postsWithLocation]
                            )
                        @endslot
                    @endif
                @endopenStreetMap
            @endif
            @section('loop')
                @if ($displayArchiveLoop)
                    @include('posts-list')
                @endif
            @show
        @else
            <div class="o-grid">
                <div class="o-grid-12">
                    @notice([
                        'type' => 'info',
                        'message' => [
                            'text' => $lang->noResult,
                            'size' => 'md'
                        ]
                    ])
                    @endnotice
                </div>
            </div>
        @endif

        @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid']])

        {!! $hook->loopEnd !!}

    </div>
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
