@includeFirst([
    'partials.archive.archive-' . sanitize_title($secondaryPostType) . '-filters',
    'partials.archive.archive-filters',
])
<div class="archive s-archive s-archive-secondary s-archive-template-{{ sanitize_title($secondaryTemplate) }}">

    {!! $hook->secondaryLoopStart !!}

    @if (!empty($posts))

        @includeIf("partials.post.post-{$secondaryTemplate}")

        @if ($showSecondaryPagination)
            @pagination([
                'list' => $secondaryPaginationList,
                'classList' => ['u-margin__top--8', 'u-display--flex', 'u-justify-content--center'],
                'current' => $currentPage,
                'linkPrefix' => '?paged='
            ])
            @endpagination
        @endif

    @endif

    {!! $hook->secondaryLoopEnd !!}

</div>
