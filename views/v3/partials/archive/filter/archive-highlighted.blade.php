@foreach ($enabledTaxonomyFilters->highlighted as $taxKey => $taxonomy)
    @if(count($taxonomy->values) > 1)

        {{-- TODO: HUR KOMMER MAN KUNNA LOOPA I EN @list komponent ?????? --}}
        <ul>
            <li class="highlighted-title">
                @typography([
                    "variant" => "h3",
                    "element" => "h3",
                ])
                    {{$taxonomy->label}}
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

    @endif
@endforeach