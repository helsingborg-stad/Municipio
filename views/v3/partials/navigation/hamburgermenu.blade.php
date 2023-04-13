@if (!empty($hamburgerMenuItems))
        @hamburgerMenu([
            'id' => 'menu-hamburger',
            'menuItems' => $hamburgerMenuItems,
            'showSearch' => $showHamburgerMenuSearch,
            'parentStyle' => $customizer->hamburgerMenuParentStyle,
            'mobile' => $customizer->hamburgerMenuMobile,
            'attributeList' => [
                'aria-hidden' => 'true'
            ]
        ])
            <div class="o-grid-12">
                @if($showHamburgerMenuSearch)
                    @form(['method' => 'get', 'action' => '/', 'classList' => ['search-form']])
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
                @endif
            </div>
        @endhamburgerMenu
@endif
