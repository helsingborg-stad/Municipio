<div class="o-container s-archive s-archive-secondary s-archive-template-{{ sanitize_title($secondaryTemplate) }}">

    {!! $hook->secondaryLoopStart !!}

    @includeFirst([
        'partials.archive.archive-filters-' . $secondaryPostType . '-secondary',
        'partials.archive.archive-filters-secondary',
    ])
    @if (!empty($secondaryQuery->posts))
        @includeIf("partials.post.post-{$secondaryTemplate}", ['posts' => $secondaryQuery->posts])

        @if ($showSecondaryPagination)
            @pagination([
                'list' => $secondaryPaginationList,
                'classList' => ['u-margin__top--8', 'u-display--flex', 'u-justify-content--center'],
                'current' => $currentPage,
                'linkPrefix' => "?$secondaryPaginationLinkPrefix",
                'anchorTag' => '#filter'
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

    {!! $hook->secondaryLoopEnd !!}

</div>
