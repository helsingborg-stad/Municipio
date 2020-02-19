@foreach ($enabledTaxonomyFilters->row as $taxKey => $taxonomy)
    @if(count($taxonomy->values) > 1)
        <ul class="segmented-control">
            <li class="title">{{ $taxonomy->label }}:</li>
            @foreach ($taxonomy->values as $term)
                <li>
                    @option([
                        'type' => $taxonomy->type === 'single' ? 'radio' : 'checkbox',
                        'attributeList' => [
                            'name' => 'filter['.$taxKey.'][]',
                            'checked' => checked(true, isset($_GET['filter'][$taxKey]) &&
                            is_array($_GET['filter'][$taxKey]) &&
                            in_array($term->slug, $_GET['filter'][$taxKey])),
                            'value' => $term->slug,
                            'id' => 'segment-id-'.$taxKey.'-'.$term->slug
                        ],
                        'label' => $term->name
                    ])
                    @endoption
                </li>
            @endforeach
        </ul>
    @endif
@endforeach
