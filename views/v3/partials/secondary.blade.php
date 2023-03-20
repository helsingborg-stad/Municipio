<div class="archive s-archive s-archive-secondary s-archive-template-{{ sanitize_title($secondaryTemplate) }}">

    {!! $hook->secondaryLoopStart !!}

    @if (!empty($posts))
        @includeFirst([
            'partials.archive.archive-filters-' . $secondaryPostType . '-secondary',
            'partials.archive.archive-filters-secondary',
        ])
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
