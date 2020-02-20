@if (is_active_sidebar('bottom-sidebar'))
    <?php dynamic_sidebar('bottom-sidebar'); ?>
@endif
<footer id="site-footer" class="{{ apply_filters('Views/Partials/Header/FooterClass', $footerLayout['classes']) }}">
    @section('footer-body')

        @if (get_field('footer_logotype_vertical_position', 'option') == 'bottom')

            <nav>
                <ul class="nav nav-help nav-horizontal">
                    {!!
                        wp_nav_menu(array(
                            'theme_location' => 'help-menu',
                            'container' => false,
                            'container_class' => 'menu-{menu-slug}-container',
                            'container_id' => '',
                            'menu_class' => '',
                            'menu_id' => 'help-menu-top',
                            'echo' => false,
                            'before' => '',
                            'after' => '',
                            'link_before' => '',
                            'link_after' => '',
                            'items_wrap' => '%3$s',
                            'depth' => 1,
                            'fallback_cb' => '__return_false'
                        ));
                    !!}
                </ul>
            </nav>

        @endif

        <div class="grid">
            <div class="{{ get_field('footer_signature_show', 'option') ? 'grid-md-10' : 'grid-md-12' }}">

                {{-- ## Footer header befin ## --}}
                @if (get_field('footer_logotype_vertical_position', 'option') == 'top' || !get_field('footer_logotype_vertical_position', 'option'))

                    @if (get_field('footer_logotype', 'option') != 'hide')
                        {!! municipio_get_logotype(get_field('footer_logotype', 'option'), false, true, false, false) !!}
                    @endif

                    <nav class="{{ !get_field('footer_signature_show', 'option') ? 'pull-right' : '' }}">
                        <ul class="nav nav-help nav-horizontal">
                            {!!
                                wp_nav_menu(array(
                                    'theme_location' => 'help-menu',
                                    'container' => false,
                                    'container_class' => 'menu-{menu-slug}-container',
                                    'container_id' => '',
                                    'menu_class' => '',
                                    'menu_id' => 'help-menu-top',
                                    'echo' => false,
                                    'before' => '',
                                    'after' => '',
                                    'link_before' => '',
                                    'link_after' => '',
                                    'items_wrap' => '%3$s',
                                    'depth' => 1,
                                    'fallback_cb' => '__return_false'
                                ));
                            !!}
                        </ul>
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
                    {!! municipio_get_logotype(get_field('footer_logotype', 'option'), false, true, false, false) !!}
                @endif
                {{-- ## Footer header end ## --}}
            </div>

            {{-- ## Footer signature ## --}}
            @if (get_field('footer_signature_show', 'option'))
                {!! apply_filters('Municipio/footer_signature', '<a href="http://www.helsingborg.se">' . $footerLogo . '</a>') !!}
            @endif
        </div>


        {{-- ## Social icons ## --}}
        @if (have_rows('footer_icons_repeater', 'option'))

            <ul class="icons-list">
                @foreach(get_field('footer_icons_repeater', 'option') as $link)
                    <li>
                        <a href="{{ $link['link_url'] }}" target="_blank" class="link-item-light">
                            {!! $link['link_icon'] !!}

                            @if (isset($link['link_title']))
                                <span class="sr-only">{{ $link['link_title'] }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>

        @endif
    @stop

</footer>

