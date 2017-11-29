<footer class="main-footer main-footer-compressed hidden-print {{ get_field('scroll_elevator_enabled', 'option') ? 'scroll-elevator-toggle' : '' }}">
    <div class="container">
        <div class="grid">
            <div class="grid-md-3">
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

                @if(have_rows('footer_icons_repeater', 'option'))
                    <ul class="icons-list gutter gutter-top">
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
            </div>

            <div class="grid-md-6">
                @if (is_active_sidebar('footer-area'))
                    <div class="grid">
                    <?php dynamic_sidebar('footer-area'); ?>
                    </div>
                @endif
            </div>

            {{-- ## Footer signature ## --}}
            @if (get_field('footer_signature_show', 'option'))
                <div class="grid-md-2 text-right">
                    {!! apply_filters('Municipio/footer_signature', '<a href="http://www.helsingborg.se"><img src="' . get_template_directory_uri() . '/assets/dist/images/helsingborg_gray.svg" alt="Helsingborg Stad" class="footer-signature"></a>') !!}
                </div>
            @endif
        </div>
    </div>
</footer>
