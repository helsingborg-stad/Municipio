<div class="search-top {!! apply_filters('Municipio/desktop_menu_breakpoint','hidden-sm'); !!} hidden-print" id="search">
    <div class="container">
        <div class="grid">
            <div class="grid-sm-12">

                @form([
                    'method' => 'get',
                    'action' => esc_url( home_url( '/' ) )
                ])


                    @field([
                        'type' => 'text',
                        'value' => get_search_query(),
                        'attributeList' => [
                        'type' => 'search',
                        'name' => 's',
                        'required' => false,
                    ],
                        'label' => _x( 'Search for:', 'label' )
                    ])
                    @endfield

                    @button([
                        'type' => 'filled',
                        'icon' => 'search',
                        'size' => 'md',
                        'color' => 'secondary',
                        'attributeList' => [
                            'type' => 'submit'
                        ]
                    ])
                    @endbutton

                @endform

            </div>
        </div>
    </div>
</div>
