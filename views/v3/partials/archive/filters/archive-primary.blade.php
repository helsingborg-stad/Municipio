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
