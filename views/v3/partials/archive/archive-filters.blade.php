@if (!empty($enabledHeaderFilters) || !empty($enabledTaxonomyFilters))

@if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'content')
    <section class="u-mb-4 sidebar-content-area archive-filters grid-xs-12">
@else
    <section class="creamy creamy-border-bottom sidebar-content-area archive-filters">
@endif

    @form([
        'method' => 'get',
        'action' => $archiveUrl
        'classList' => ['container','u-w-100'],
        'id' => 'archive-filter'
    ])

        @if (isset($enabledTaxonomyFilters->highlighted) && !empty($enabledTaxonomyFilters->highlighted))
            @includeIf('partials.archive.filter.archive-highlighted')
        @endif

        @if (in_array('text_search', $enabledHeaderFilters))
            @includeIf('partials.archive.filter.archive-search')
        @endif

        @if (in_array('date_range', $enabledHeaderFilters))
            @includeIf('partials.archive.filter.archive-date-range')
        @endif

        @if (isset($enabledTaxonomyFilters->primary) && !empty($enabledTaxonomyFilters->primary))
            @includeIf('partials.archive.filter.archive-primary')
        @endif

        @if($queryString)

                @button([
                    'type' => 'filled',
                    'icon' => 'close',
                    'size' => 'md',
                    'href' => $archiveUrl
                ])
                    _e('Clear filters', 'municipio')
                @endbutton

        @endif

        @button([
            'text' => 'Primary filled',
            'color' => 'primary',
            'type' => 'filled',
            'attributeList' => ['type' => 'submit']

        ])
            _e('Search', 'municipio')
        @endbutton

        @if (isset($enabledTaxonomyFilters->row) && !empty($enabledTaxonomyFilters->row))
            @includeIf('partials.archive.filter.archive-row')
        @endif

        @if (isset($enabledTaxonomyFilters->folded) && !empty($enabledTaxonomyFilters->folded))
            @includeIf('partials.archive.filter.archive-folded')
        @endif

    @endform
</section>
@endif
