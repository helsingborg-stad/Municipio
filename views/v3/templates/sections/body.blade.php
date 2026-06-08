@element([
    'componentElement' => 'body',
    'classList' => $classes ?? explode(' ', $bodyClass),
    'attributeList' => array_merge(
        [
            'data-js-page-id' => $pageID,
            'data-js-post-type' => $postType,
            'data-scope' => $postTypeScope ?? null,
        ],
        ($customizer->headerSticky === 'sticky' && empty($headerData['nonStickyMegaMenu'])) ? [
            'data-js-toggle-item' => 'mega-menu',
            'data-js-toggle-class' => 'mega-menu-open',
        ] : []
    )
])
    @yield('body-content')

    @include('templates.sections.styleguide-customize')
    
@endelement