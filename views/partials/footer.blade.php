<footer class="main-footer">
    <div class="container">

        <!-- Logotype -->
        @if (get_field('footer_logotype_vertical_position', 'option') == 'top' || get_field('footer_logotype_vertical_position', 'option') == '' || get_field('footer_logotype_vertical_position', 'option') == false)
        <div class="grid">
            @if (!get_field('footer_logotype_horizontal_position', 'option') || get_field('footer_logotype_horizontal_position', 'option') == 'left')
            <div class="grid-md-6">
                {!! municipio_get_logotype(get_field('footer_logotype', 'option')) !!}
            </div>
            @endif

            <div class="grid-md-6 {{ (get_field('footer_logotype_horizontal_position', 'option') == 'left') ? 'text-right' : '' }}">
                <nav>
                    <ul class="nav nav-help nav-horizontal">
                        @if (get_field('show_google_translate', 'option') == 'footer')
                            <li><a href="#translate">Translate</a></li>
                        @endif

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
            </div>

            @if (get_field('footer_logotype_horizontal_position', 'option') == 'right')
            <div class="grid-md-6">
                {!! municipio_get_logotype(get_field('footer_logotype', 'option')) !!}
            </div>
            @endif
        </div>
        @endif

        <!-- Widgets -->
        <div class="grid">
            @if (is_active_sidebar('footer-area'))
                <?php dynamic_sidebar('footer-area'); ?>
            @endif
        </div>

        <!-- Footer links -->
        @if(function_exists('have_rows'))
            @if(have_rows('footer_icons_repeater', 'option'))
                <div class="grid">
                    <div class="grid-xs-12">
                        <ul class="icons-list text-center gutter-margin text-xl">
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
                    </div>
                </div>
            @endif
        @endif

        @if (get_field('footer_logotype_vertical_position', 'option') == 'bottom')
        <div class="grid">
            <div class="grid-lg-12 {{ ($pos = get_field('footer_logotype_horizontal_position', 'option')) ? 'text-' . $pos : '' }}">
                {!! municipio_get_logotype(get_field('footer_logotype', 'option')) !!}
            </div>
        </div>
        @endif

    </div>
</footer>
