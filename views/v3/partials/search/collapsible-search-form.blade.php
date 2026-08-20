@php
    $placeholderClasses = implode(' ', array_filter(array_merge([
        'collapsible-search-placeholder',
    ], $classList ?? [])));
@endphp

<div class="{{ $placeholderClasses }}">[COLLAPSIBLE_SEARCH]</div>