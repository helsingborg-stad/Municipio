@if (!empty($headerData))
    @include('partials.header.skip-to-main-content')
    @includeWhen($hasMainMenu, 'partials.header.skip-to-main-menu')
    @includeWhen($hasSideMenu, 'partials.header.skip-to-side-menu')

    @foreach($headerData as $key => $header)
        @if($header->isEmpty())
            @continue
        @endif

        @scope(['name' => ['header-flexible-'. $key, 'header-flexible', 'header']])
            @header([
                'classList' => array_merge($header->getCssClasses(), ['header', 'c-header--flexible']),
                'id' => 'site-header-flexible-' . $key,
                'sticky' => $header->isSticky(),
                'context' => 'site.header.flexible.' . $key,
                'attributeList' => $header->getAttributes(),
            ])
                @element([
                    'classList' => [
                        'c-header__main-'. $key .'-area'
                    ]
                ])
                    @foreach(['left', 'center', 'right'] as $alignment)
                        @continue(!$header->hasMenuItemsForAlignment($alignment))
                            @element([
                                'classList' => [
                                    'c-header__' . $key .'-'. $alignment
                                ]
                            ])
                                @foreach($header->getMenuItemsForAlignment($alignment) as $menuItem)
                                    @includeIf('partials.header.components.' . $menuItem->getType())
                                @endforeach
                        @endelement
                    @endforeach
                @endelement
            @endheader
        @endscope
    @endforeach

    @if($hasMegaMenu && !empty($megaMenu['items']))
        @include('partials.navigation.megamenu')
    @endif

    @if($hasSearchModal)
        @include('partials.search.search-modal')
    @endif
@endif
