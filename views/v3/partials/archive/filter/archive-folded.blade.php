<div class="gutter gutter-top" id="options" style="display: none;">
    <div class="grid" data-equal-container>
        @foreach ($enabledTaxonomyFilters->folded as $taxKey => $taxonomy)
            <div class="grid-md-4">
                <div class="box box-panel box-panel-secondary" data-equal-item>
                    @typography([
                        "variant" => "h4",
                        "element" => "h4",
                    ])
                        {{$taxonomy->label}}
                    @endtypography
                    <div class="box-content">
                        <?php $taxonomy->slug = $taxKey; $dropdown = \Municipio\Content\PostFilters::getMultiTaxDropdown($taxonomy, 0, 'list-hierarchical'); ?>
                        {!! $dropdown !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@button([
    'text' => 'Primary filled',
    'color' => 'primary',
    'type' => 'filled',
    'attributeList' => ['data-toggle' => '#options']
])
    _e('Search', 'municipio')
@endbutton

{{-- TODO: TOOGLE ALTERNATIV FÖR IKON ÁTT BLI MINUS --}}
@button([
        'type' => 'filled',
        'icon' => 'add_circle',
        'size' => 'md',
        'attributeList' => [
            'data-toggle' => '#options',
            'data-toggle-text' => 'Visa färre sökalternativ'
        ]
    ])
        Visa fler sökalternativ…
@endbutton
