@if (!empty($megaMenu['items']))
    @megaMenu([
        'id' => 'mega-menu',
        'menuItems' => $megaMenu['items'],
        'mobile' => $customizer->megaMenuMobile,
        'attributeList' => [
            'aria-hidden' => 'true',
        ],
        'context' => ['site.megamenu.nav']

    ])
        @if($showMegaMenuSearch)
            <div class="o-grid-12">
                @form(['method' => 'get', 'action' => '/', 'classList' => ['search-form']])
                    <label for="mega-menu-search" class="u-sr__only">
                        {{ $lang->searchOn . " " . $siteName }}
                    </label>
                    @group([])
                        @field([
                            'id' => 'mega-menu-search',
                            'type' => 'search',
                            'name' => 's',
                            'placeholder' => $lang->searchOn . " " . $siteName,
                            'required' => true,
                            'attributeList' => [
                                'aria-label' => $lang->searchOn . " " . $siteName
                            ],
                        ])
                        @endfield
                        @button([
                            'color' => 'primary',
                            'type' => 'submit',
                            'text' => $lang->search
                        ])
                        @endbutton
                    @endgroup
                @endform
            </div>
        @endif
    @endmegaMenu
@endif
