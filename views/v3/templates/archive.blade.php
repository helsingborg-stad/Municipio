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

    @includeFirst([
        'partials.archive.archive-' . sanitize_title($postType) . '-filters',
        'partials.archive.archive-filters',
    ])

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
            @if(isset($renderedPostObjects) && $renderedPostObjects && in_array($template, ['cards', 'grid', 'compressed', 'schema-project']) )
                <div class="o-grid">
                    {!! $renderedPostObjects !!}
                </div>
            @elseif(isset($renderedPostObjects) && $renderedPostObjects && $template === 'newsitem')
                <div class="arcive-news-items o-grid">
                    {!! $renderedPostObjects !!}
                </div>
            @elseif(isset($renderedPostObjects) && $renderedPostObjects && $template === 'collection')
                @collection([
                    'unbox' => true,
                    'classList' => ['o-grid', 'o-grid--horizontal']
                ])
                    {!! $renderedPostObjects !!}
                @endcollection
            @elseif($displayArchiveLoop)
                @includefirst(
                    [   
                        'partials.post.schema.' . $template,
                        'partials.post.' . $postType . '-' . $template, 
                        'partials.post.post-' . $template, 
                        'partials.post.post-cards'
                    ],
                    ['posts' => $posts]
                )
            @endif
            @if ($showPagination && $paginationList)
                @pagination([
                    'list' => $paginationList,
                    'classList' => ['u-margin__top--8', 'u-display--flex', 'u-justify-content--center'],
                    'current' => $currentPage,
                    'linkPrefix' => 'paged'
                ])
                @endpagination
            @endif
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
