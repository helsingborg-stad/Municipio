@openStreetMap([
    'pins' => $posts,
    'startPosition' => ['lat' => 52.520008, 'lng' => 13.404954, 'zoom' => 12],
    'mapStyle' => $mapStyle,
    'fullWidth' => true,
    'containerAware' => true
])
    @if ($posts)
        @slot('sidebarContent')
            @collection([
                'classList' => ['c-collection--posts', 'o-grid'],
                'attributeList' => [
                    'js-pagination-container' => ''
                ]
            ])
                @foreach ($posts as $post)
                    <div class="{{ $postsColumns }}" js-pagination-item>
                        @include('partials.openstreetmap.partials.collection')
                        @include('partials.openstreetmap.partials.post')
                    </div>
                @endforeach
            @endcollection

            @include('partials.openstreetmap.partials.pagination', [
                'perPage' => $postsPerPage,
            ])
        @endslot
    @endif
@endopenStreetMap
