@if (!empty($hamburgerMenuItems))
    <div class="o-container o-container--wide">
        @hamburgerMenu([
            'menuItems' => $hamburgerMenuItems,
            'showSearch' => $showHamburgerMenuSearch,
            'parentStyle' => $customizer->hamburgerMenuParentStyle,
            'mobile' => $customizer->hamburgerMenuMobile,
        ])

        <div class="o-grid-12">
            @form(['method' => 'get', 'action' => '/'])
                <label for="hamburger-menu-search" class="u-sr__only">{{ __('Search', 'component-library') }}</label>

                @group([])
                    @field([
                        'id' => 'hamburger-menu-search',
                        'type' => 'text',
                        'attributeList' => [
                            'type' => 'text',
                            'name' => 's',
                        ],
                        'placeholder' => $lang->searchOn . " " . $siteName,
                        'required' => true,
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

        @endhamburgerMenu
    </div>
@endif
