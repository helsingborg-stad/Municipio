@extends('templates.single')

@section('helper-navigation')
    @includeWhen($helperNavBeforeContent, 'partials.navigation.helper')
@stop

@section('content')

    @if ($archiveTitle || $archiveLead)
        <article class="c-article c-article--readable-width s-article u-clearfix" id="article">
            @if ($archiveTitle)
                @typography([
                    'variant' => 'h1',
                    'element' => 'h1',
                    'classList' => ['t-archive-title', 't-' . $postType . '-archive-title']
                ])
                    {{ $archiveTitle }}
                @endtypography
            @endif
            @if ($archiveLead)
                @typography([
                    'variant' => 'p',
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

            @includefirst(
                ['partials.post.' . $postType . '-' . $template, 'partials.post.post-' . $template],
                ['posts' => $posts]
            )

            @if ($showPagination)
                @pagination([
                    'list' => $paginationList,
                    'classList' => ['u-margin__top--8', 'u-display--flex', 'u-justify-content--center'],
                    'current' => $currentPage,
                    'linkPrefix' => '?paged='
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
