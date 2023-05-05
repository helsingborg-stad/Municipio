<!-- partials.openstreetmap.map -->
<!-- TODO: Add posts column -->
@openStreetMap([
    'pins' => $pins
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
                    <div class="openstreetmap__post-container" js-pagination-item>
                        @include('partials.openstreetmap.partials.collection')
                        @include('partials.openstreetmap.partials.post')
                    </div>
                @endforeach
            @endcollection

            @include('partials.openstreetmap.partials.pagination')
        @endslot
    @endif
@endopenStreetMap
