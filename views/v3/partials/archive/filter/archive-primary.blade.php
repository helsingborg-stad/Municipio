@foreach ($enabledTaxonomyFilters->primary as $taxKey => $tax)
    <div class="grid-sm-12 {{ $tax->type == 'multi' ? 'grid-md-fit-content' : 'grid-md-auto' }}">
        <label for="filter-{{ $taxKey }}" class="text-sm sr-only">{{ $tax->label }}</label>
        @if ($tax->type === 'single')
            @includeIf('partials.archive.archive-filters.select')
        @else
            @includeIf('partials.archive.archive-filters.button-dropdown')
        @endif
    </div>
@endforeach
