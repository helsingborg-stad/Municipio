<!-- partials.openstreetmap.map -->
<!-- TODO: Add posts column -->
@openStreetMap([
    'pins' => $pins
])
    @if ($postsWithLocation)
        @slot('sidebarContent')
            @includefirst(
                ['partials.post.' . $postType . '-' . $template, 'partials.post.post-' . $template],
                ['posts' => $postsWithLocation]
            )
        @endslot
    @endif
@endopenStreetMap
