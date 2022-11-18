@if (!empty($hamburgerMenuItems))
    <div class="o-container o-container--wide">
        @hamburgerMenu([
            'menuItems' => $hamburgerMenuItems,
            'showSearch' => $showHamburgerMenuSearch,
            'parentStyle' => $customizer->hamburgerMenuParentStyle,
            'mobile' => $customizer->hamburgerMenuMobile,
            'attributeList' => [
                'aria-hidden' => 'true'
            ]
        ])
            <div class="o-grid-12">
                @form(['method' => 'get', 'action' => '/'])
                    <label for="hamburger-menu-search" class="u-sr__only">
                        {{ $lang->searchOn . " " . $siteName }}
                    </label>
                    @group([])
                        @field([
                            'id' => 'hamburger-menu-search',
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
        @endhamburgerMenu
    </div>
@endif
