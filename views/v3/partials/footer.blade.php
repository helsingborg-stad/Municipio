@if (is_active_sidebar('bottom-sidebar'))
    <div class="o-container o-container--fullwidth o-container--remove-spacing">
        <div class="o-grid bottom-sidebar">
            <?php dynamic_sidebar('bottom-sidebar'); ?>
        </div>
    </div>
@endif

@footer([
    'id' => 'site-footer',
    'slotOnly' => true,
    'logotype' => $footerLogotype ?? false,
    'logotypeHref' => $homeUrl,
    'subfooterLogotype' => $subfooterLogotype,
    'context' => 'component.footer',
    'classList' => apply_filters(
        'Views/Partials/Header/FooterClass',
        [
            'site-footer',
            's-footer'
        ]
    ),
])

{{-- Before footer body --}}
@yield('before-footer-body')

{{-- Footer body --}}
@section('footer-body')
    
    {{-- ## Footer top widget area begin ## --}}
    @if (is_active_sidebar('footer-area-top'))
        @slot('prefooter')
            @include('partials.sidebar', ['id' => 'footer-area-top', 'classes' => ['o-grid']])
        @endslot
    @endif

    @if (get_field('footer_logotype_vertical_position', 'option') == 'bottom')
        <div class="o-container">
            <div class="o-grid u-print-display--none">
                <div class="o-grid-12">
                    <nav>
                        <ul class="nav nav-help nav-horizontal">
                            {!!
                                wp_nav_menu([
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
                                    'fallback_cb' => '__return_false',
                                ]);
                            !!}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    @endif

    @slot('footerareas')
        @foreach ($footerAreas as $footerAreaId)
            @if (is_active_sidebar($footerAreaId))
                <div class="o-grid-{{ $footerGridSize }}@md {{ $footerTextAlignment }}">
                    @include('partials.sidebar', [
                        'id' => $footerAreaId,
                        'classes' => ['o-grid', 'c-footer__widget-area'],
                    ])
                </div>
            @endif
        @endforeach
    @endslot

    {{-- ## Footer bottom widget area begin ## --}}
    @if (is_active_sidebar('footer-area-bottom'))
        @slot('postfooter')
            @include('partials.sidebar', ['id' => 'footer-area-bottom', 'classes' => ['o-grid']])
        @endslot
    @endif

@show

{{-- After footer body --}}
@yield('after-footer-body')

@endfooter
