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
        @foreach ($enabledTaxonomyFilters->highlighted as $taxKey => $taxonomy)
            @if(count($taxonomy->values) > 1)
            <div class="gutter gutter-top">
            <div class="grid">
                <div class="grid-xs-12">
                    {{-- TODO: HUR KOMMER MAN KUNNA LOOPA I EN @list komponent ?????? --}}
                    <ul>
                        <li class="highlighted-title">
                            @typography([
                                "variant" => "h3",
                                "element" => "h3",
                            ])
                                $taxonomy->label
                            @endtypography
                        </li>
                        <ul class="nav nav-pills nav-horizontal nav-pills--badge">
                        @foreach ($taxonomy->values as $term)
                            <li>

                                @option([
                                    'type' => 'checkbox',
                                    'attributeList' => [
                                        'name' => 'filter['.$taxKey.'][]',
                                        'checked' => checked(true, isset($_GET["filter"][$taxKey])
                                                && is_array($_GET["filter"][$taxKey])
                                            && in_array($term->slug, $_GET["filter"][$taxKey])),
                                        'value' => $term->slug,
                                        'id' => 'segment-id-'.$taxKey.'-'.$term->slug
                                    ],
                                    'label' => $term->name
                                ])
                                @endoption

                            </li>
                        @endforeach
                        </ul>
                    </ul>
                </div>
            </div>
            </div>
            @endif
        @endforeach
    @endif

        <div class="grid">
            @if (in_array('text_search', $enabledHeaderFilters))
            <div class="grid-sm-12 grid-md-auto">
                <div class="input-group">

                    @icon([
                        'icon' => 'search',
                        'size' => 'xl',
                        'color' => 'Primary'
                    ])
                    @endicon

                    @field([
                        'type' => 'text',
                        'attributeList' => [
                            'type' => 'search',
                            'name' => 's',
                            'required' => false,
                            'id' => 'filter-keyword',
                            'value' => $searchQuery
                        ],
                        'label' => _e('Search', 'municipio'),
                        'classList' => ['text-sm' 'sr-only']
                    ])
                    @endfield

                </div>

            </div>
            @endif

            @if (in_array('date_range', $enabledHeaderFilters))
            <div class="grid-sm-12 grid-md-auto">
                <div class="input-group">

                    @field([
                        'type' => 'datepicker',
                        'value' => isset($_GET['from']) && !empty($_GET['from']) ?
                                        sanitize_text_field($_GET['from']) : '',
                        'label' =>  _e('Date published', 'municipio'),
                        'id' => 'filter-date-from',
                        'name' => 'from',
                        'attributeList' => [
                            'type' => 'text',
                            'name' => 'text',
                            'data-invalid-message' => "You need to add a valid date!",
                            'readonly' => 'readonly'
                        ],
                        'required' => false,
                        'classList' => [
                            'form-control',
                            'datepicker-range',
                            'datepicker-range-from'
                        ],
                        'datepicker' => [
                            'title'                 => 'Välj ett datum',
                            'minDate'               => false,
                            'maxDate'               => false,
                            'required'              => true,
                            'showResetButton'       => true,
                            'showDaysOutOfMonth'    => true,
                            'showClearButton'       => true,
                            'hideOnBlur'            => true,
                            'hideOnSelect'          => false,
                        ]
                    ])
                    @endfield

                    @field([
                        'type' => 'datepicker',
                        'value' => isset($_GET["to"]) && !empty($_GET["to"]) ?
                                        sanitize_text_field($_GET["to"]) : '',
                        'label' =>   _e('To date', 'municipio'),
                        'id' => 'filter-date-from',
                        'name' => 'to',
                        'attributeList' => [
                            'type' => 'text',
                            'name' => 'text',
                            'data-invalid-message' => "You need to add a valid date!",
                            'readonly' => 'readonly'
                        ],
                        'required' => false,
                        'classList' => [
                            'form-control',
                            'datepicker-range',
                            'datepicker-range-to'
                        ],
                        'datepicker' => [
                            'title'                 => 'Välj ett datum',
                            'minDate'               => false,
                            'maxDate'               => false,
                            'required'              => true,
                            'showResetButton'       => true,
                            'showDaysOutOfMonth'    => true,
                            'showClearButton'       => true,
                            'hideOnBlur'            => true,
                            'hideOnSelect'          => false,
                        ]
                    ])
                    @endfield

                </div>
            </div>
            @endif

            @if (isset($enabledTaxonomyFilters->primary) && !empty($enabledTaxonomyFilters->primary))
                @foreach ($enabledTaxonomyFilters->primary as $taxKey => $tax)
                <div class="grid-sm-12 {{ $tax->type == 'multi' ? 'grid-md-fit-content' : 'grid-md-auto' }}">
                    <label for="filter-{{ $taxKey }}" class="text-sm sr-only">{{ $tax->label }}</label>
                    @if ($tax->type === 'single')
                        @include('partials.archive-filters.select')
                    @else
                        @include('partials.archive-filters.button-dropdown')
                    @endif
                </div>
                @endforeach
            @endif

            @if($queryString)
                <div class="grid-sm-12 hidden-sm hidden-xs grid-md-fit-content">
                    <a class="btn btn-block pricon pricon-close pricon-space-right" href="{{ $archiveUrl }}"><?php _e('Clear filters', 'municipio'); ?></a>
                </div>
            @endif
            <div class="grid-sm-12 grid-md-fit-content">
                <input type="submit" value="<?php _e('Search', 'municipio'); ?>" class="btn btn-primary btn-block">
            </div>
        </div>

        @if (isset($enabledTaxonomyFilters->row) && !empty($enabledTaxonomyFilters->row))
        @foreach ($enabledTaxonomyFilters->row as $taxKey => $taxonomy)
        @if(count($taxonomy->values) > 1)
        <div class="gutter gutter-top">
        <div class="grid">
            <div class="grid-xs-12">
                <ul class="segmented-control">
                    <li class="title">{{ $taxonomy->label }}:</li>
                    @foreach ($taxonomy->values as $term)
                        <li>
                            <input id="segment-id-{{ $taxKey }}-{{ $term->slug }}" type="{{ $taxonomy->type === 'single' ? 'radio' : 'checkbox' }}" name="filter[{{ $taxKey }}][]" value="{{ $term->slug }}" {{ checked(true, isset($_GET['filter'][$taxKey]) && is_array($_GET['filter'][$taxKey]) && in_array($term->slug, $_GET['filter'][$taxKey])) }}>
                            <label for="segment-id-{{ $taxKey }}-{{ $term->slug }}" class="checkbox inline-block">{{ $term->name }}</label>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        </div>
        @endif
        @endforeach
        @endif

        @if (isset($enabledTaxonomyFilters->folded) && !empty($enabledTaxonomyFilters->folded))
        <div class="gutter gutter-top" id="options" style="display: none;">
            <div class="grid" data-equal-container>
            @foreach ($enabledTaxonomyFilters->folded as $taxKey => $taxonomy)
                <div class="grid-md-4">
                    <div class="box box-panel box-panel-secondary" data-equal-item>
                        <h4 class="box-title">{{ $taxonomy->label }}</h4>
                        <div class="box-content">
                            <?php $taxonomy->slug = $taxKey; $dropdown = \Municipio\Content\PostFilters::getMultiTaxDropdown($taxonomy, 0, 'list-hierarchical'); ?>
                            {!! $dropdown !!}
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
        @endif

        @if (isset($enabledTaxonomyFilters->folded) && !empty($enabledTaxonomyFilters->folded))
        <div class="grid no-margin gutter gutter-top gutter-sm">
            <div class="grid-xs-12">
                <button type="button" data-toggle="#options" class="btn btn-plain pricon pricon-plus-o pricon-space-right" data-toggle-text="Visa färre sökalternativ…" data-toggle-class="btn btn-plain pricon pricon-minus-o pricon-space-right">Visa fler sökalternativ…</a>
            </div>
        </div>
        @endif

    @endform
</section>
@endif
