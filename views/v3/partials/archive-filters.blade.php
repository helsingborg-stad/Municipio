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
            @include('partials.archive.filters.archive-highlighted')
        @endif

        <div class="grid">

            @if (in_array('text_search', $enabledHeaderFilters))
                @include('partials.archive.filters.archive-search')
            @endif

            @if (in_array('date_range', $enabledHeaderFilters))
                @include('partials.archive.filters.archive-date-range')
            @endif

            @if (isset($enabledTaxonomyFilters->primary) && !empty($enabledTaxonomyFilters->primary))
                @include('partials.archive.filters.archive-primary')
            @endif

            @if($queryString)
                <div class="grid-sm-12 hidden-sm hidden-xs grid-md-fit-content">
                    @button([
                        'type' => 'filled',
                        'icon' => 'close',
                        'size' => 'md',
                        'href' => $archiveUrl
                    ])
                        _e('Clear filters', 'municipio')
                    @endbutton

                </div>
            @endif
            <div class="grid-sm-12 grid-md-fit-content">
                @button([
                    'text' => 'Primary filled',
                    'color' => 'primary',
                    'type' => 'filled',
                    'attributeList' => ['type' => 'submit']

                ])
                    _e('Search', 'municipio')
                @endbutton
                <input type="submit" value="<?php _e('Search', 'municipio'); ?>" class="btn btn-primary btn-block">
            </div>
        </div>
        @if (isset($enabledTaxonomyFilters->row) && !empty($enabledTaxonomyFilters->row))
            @include('partials.archive.filters.archive-row')
        @endif

        @if (isset($enabledTaxonomyFilters->folded) && !empty($enabledTaxonomyFilters->folded))
            @include('partials.archive.filters.archive-folded')
        @endif

    @endform
</section>
@endif
