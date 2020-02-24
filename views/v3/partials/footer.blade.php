@if (is_active_sidebar('bottom-sidebar'))
    <?php dynamic_sidebar('bottom-sidebar'); ?>
@endif

<footer id="site-footer" class="{{ apply_filters('Views/Partials/Header/FooterClass', $footerLayout['classes']) }}">
    @section('footer-body')

        @if (get_field('footer_logotype_vertical_position', 'option') == 'bottom')

            <nav>
                {{-- NAVIGATION HELPER NAV --}}
                @includeIf('partials.navigation.helper-nav')
            </nav>

        @endif



                {{-- ## Footer header befin ## --}}
                @if (get_field('footer_logotype_vertical_position', 'option') == 'top' || !get_field('footer_logotype_vertical_position', 'option'))

                    @if (get_field('footer_logotype', 'option') != 'hide')
                        {!! municipio_get_logotype(get_field('footer_logotype', 'option'), false, true, false, false) !!}
                    @endif

                    <nav class="{{ !get_field('footer_signature_show', 'option') ? 'pull-right' : '' }}">
                        {{-- NAVIGATION HELPER NAV --}}
                        @includeIf('partials.navigation.helper-nav')
                    </nav>

                @endif
                {{-- ## Footer header end ## --}}

                {{-- ## Footer widget area begin ## --}}

                @if (is_active_sidebar('footer-area'))
                    <?php dynamic_sidebar('footer-area'); ?>
                @endif

                {{-- ## Footer widget area end ## --}}

                {{-- ## Footer header begin ## --}}
                @if (get_field('footer_logotype_vertical_position', 'option') == 'bottom' && get_field('footer_logotype', 'option') != 'hide')
                    @if (get_field('header_logotype', 'option') === 'negative')
                        @includeIf('partials.logo', [
                            'logo' => get_field('logotype_negative', 'option'),
                            'logoTooltip' => get_field('logotype_tooltip', 'option')
                            ])
                    @else
                        @includeIf('partials.logo', [
                            'logo' => get_field('logotype', 'option'),
                            'logoTooltip' => get_field('logotype_tooltip', 'option')
                            ])
                    @endif
                @endif
                {{-- ## Footer header end ## --}}


            {{-- ## Footer signature ## --}}
            @if (get_field('footer_signature_show', 'option'))
                {!! apply_filters('Municipio/footer_signature', '<a href="http://www.helsingborg.se">' . $footerLogo . '</a>') !!}
            @endif



        {{-- ## Social icons ## --}}
        @if (have_rows('footer_icons_repeater', 'option'))

            <ul class="icons-list">
                @foreach(get_field('footer_icons_repeater', 'option') as $link)
                    <li>

                        @link([
                            'href' =>  $link['link_url']
                        ])
                            {{$link['link_icon']}}


                            @if (isset($link['link_title']))
                                @typography([
                                    'element' => 'span'
                                ])
                                {{ $link['link_title'] }}
                                @endtypography

                            @endif
                        @endlink

                    </li>
                @endforeach
            </ul>

        @endif


    @stop

</footer>

