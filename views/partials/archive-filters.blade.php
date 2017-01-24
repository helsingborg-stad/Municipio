@if (!empty($enabledHeaderFilters))
<section class="creamy creamy-border-bottom gutter-lg gutter-vertical sidebar-content-area archive-filters">
    <form method="get" action="{{ get_post_type_archive_link($postType) }}" class="container" id="archive-filter">
        <div class="grid">
            @if (in_array('text_search', $enabledHeaderFilters))
            <div class="grid-sm-12 grid-md-auto">
                <label for="filter-keyword" class="text-sm sr-only"><strong><?php _e('Search', 'municipio'); ?>:</strong></label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    <input type="text" name="s" id="filter-keyword" class="form-control" value="{{ !empty(get_search_query()) ? get_search_query() : '' }}" placeholder="<?php _e('Search', 'municipio'); ?>">
                </div>
            </div>
            @endif

            @if (in_array('date_range', $enabledHeaderFilters))
            <div class="grid-sm-12 grid-md-auto">
                <label for="filter-date-from" class="text-sm sr-only"><strong><?php _e('Date published', 'municipio'); ?>:</strong></label>
                <div class="input-group">
                    <span class="input-group-addon"><?php _e('From', 'municipio'); ?>:</span>
                    <input type="text" name="from" placeholder="<?php _e('From date', 'municipio'); ?>…" id="filter-date-from" class="form-control datepicker-range datepicker-range-from" value="{{ isset($_GET['from']) && !empty($_GET['from']) ? sanitize_text_field($_GET['from']) : '' }}" readonly>
                    <span class="input-group-addon"><?php _e('To', 'municipio'); ?>:</span>
                    <input type="text" name="to" placeholder="<?php _e('To date', 'municipio'); ?>" class="form-control datepicker-range datepicker-range-to" value="{{ isset($_GET['to']) && !empty($_GET['to']) ? sanitize_text_field($_GET['to']) : '' }}" readonly>
                </div>
            </div>
            @endif

            @if (isset($enabledTaxonomyFilters->primary) && !empty($enabledTaxonomyFilters->primary))
            @foreach ($enabledTaxonomyFilters->primary as $taxKey => $tax)
            <div class="grid-sm-12 {{ $tax->type == 'multi' ? 'grid-md-fit-content' : 'grid-md-auto' }}">
                <label for="filter-{{ $taxKey }}" class="text-sm sr-only">{{ $tax->label }}</label>
                @if ($tax->type === 'single')
                    <select name="term[]">
                        <option value=""><?php printf(__('Select') . ' %s…', $tax->label); ?></option>
                        @foreach ($tax->values as $term)
                        <option value="{{ $taxKey }}|{{ $term->slug }}" {{ selected(true, isset($_GET['term']) && is_array($_GET['term']) && in_array($taxKey . '|' . $term->slug, $_GET['term'])) }}>{{ $term->name }}</option>
                        @endforeach
                    </select>
                @else
                    <div class="pos-relative">
                        <button type="button" class="btn" data-dropdown=".dropdown-{{ $taxKey }}"><?php printf(__('Select') . ' %s…', $tax->label); ?></button>
                        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-arrow-right dropdown-{{ $taxKey }}" style="right:0;">
                            <ul>
                                @foreach ($tax->values as $term)
                                <li>
                                    <label class="checkbox">
                                        <input type="{{ $tax->type === 'single' ? 'radio' : 'checkbox' }}" name="term[]" value="{{ $taxKey }}|{{ $term->slug }}" {{ checked(true, isset($_GET['term']) && is_array($_GET['term']) && in_array($taxKey . '|' . $term->slug, $_GET['term'])) }}> {{ $term->name }}
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
            @endforeach
            @endif

            <div class="grid-sm-12 grid-md-fit-content">
                <input type="submit" value="<?php _e('Search', 'municipio'); ?>" class="btn btn-primary btn-block">
            </div>
        </div>

        @if (isset($enabledTaxonomyFilters->secondary) && !empty($enabledTaxonomyFilters->secondary))
        <div class="gutter gutter-top" id="options" style="display: none;">
            <div class="grid" data-equal-container>
            @foreach ($enabledTaxonomyFilters->secondary as $taxKey => $taxonomy)
                <div class="grid-md-4">
                    <div class="box box-panel box-panel-secondary" data-equal-item>
                        <h4 class="box-title">{{ $taxonomy->label }}</h4>
                        <div class="box-content">
                            <ul>
                            @foreach ($taxonomy->values as $term)
                                <li>
                                    <label class="checkbox">
                                        <input type="{{ $taxonomy->type === 'single' ? 'radio' : 'checkbox' }}" name="term[]" value="{{ $taxKey }}|{{ $term->slug }}" {{ checked(true, isset($_GET['term']) && is_array($_GET['term']) && in_array($taxKey . '|' . $term->slug, $_GET['term'])) }}> {{ $term->name }}
                                    </label>
                                </li>
                            @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
        @endif

        @if (isset($enabledTaxonomyFilters->secondary) && !empty($enabledTaxonomyFilters->secondary))
        <div class="grid no-margin gutter gutter-top gutter-sm">
            <div class="grid-xs-12">
                <button type="button" data-toggle="#options" class="btn btn-plain pricon pricon-plus-o pricon-space-right" data-toggle-text="Visa färre sökalternativ…" data-toggle-class="btn btn-plain pricon pricon-minus-o pricon-space-right">Visa fler sökalternativ…</a>
            </div>
        </div>
        @endif
    </form>
</section>
@endif
