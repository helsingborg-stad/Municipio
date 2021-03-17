@if (is_active_sidebar('bottom-sidebar'))
<div class="o-container o-container--fullwidth">
    <div class="o-grid bottom-sidebar">
        <?php dynamic_sidebar('bottom-sidebar'); ?>
    </div>
</div>
@endif

<footer id="site-footer" class="{{ apply_filters('Views/Partials/Header/FooterClass', 'site-footer') }}">
    
    {{-- Before footer body --}}
    @yield('before-footer-body')

    {{-- Footer body --}}
    @section('footer-body')
        {{-- ## Footer top widget area begin ## --}}
        @if (is_active_sidebar('footer-area-top'))
        <div class="o-container">
                <div class="o-grid-12">
                    <div class="o-grid sidebar-footer-area-top">
                            <?php dynamic_sidebar('footer-area-top'); ?>
                    </div>
                </div>
        </div>
        @endif

        <div class="o-container">
            @if (get_field('footer_logotype_vertical_position', 'option') == 'bottom')
                <div class="o-grid u-print-display--none">
                    <div class="o-grid-12">
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
                    </div>
                </div>
            @endif

            <div class="o-grid">
                <div class="o-grid-12">
                    @link(['href' => $homeUrl, 'classList' => ['u-margin__right--auto']])
                        @logotype([
                            'id' => 'footer-logotype',
                            'src'=> $logotype->standard['url'],
                            'alt' => $lang->goToHomepage,
                            'classList' => ['site-footer__logo']
                        ])
                        @endlogotype
                    @endlink
                </div>

                {{-- ## Footer widget area begin ## --}}
                @if (is_active_sidebar('footer-area'))
                    <div class="o-grid-12">
                        <div class="o-grid sidebar-footer-area">
                                <?php dynamic_sidebar('footer-area'); ?>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @show

    {{-- After footer body --}}
    @yield('after-footer-body')
</footer>