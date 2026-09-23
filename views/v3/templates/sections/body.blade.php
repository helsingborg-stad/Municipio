@element([
    'componentElement' => 'body',
    'classList' => $classes ?? explode(' ', $bodyClass),
    'attributeList' => array_merge(
        [
            'data-js-page-id' => $pageID,
            'data-js-post-type' => $postType,
            'data-scope' => $postTypeScope ?? null,
        ]
    )
])
    @yield('body-content')
@endelement