@footer([
    'id' => 'site-footer',
    'slotOnly' => true,
    'logotype' => $footerLogotype ?? false,
    'logotypeHref' => $homeUrl,
    'subfooterLogotype' => $subfooterLogotype,
    'context' => 'component.footer',
    'classList' => [
        'site-footer',
        's-footer'
    ]
])

{{-- Before footer body --}}
@yield('before-footer-body')

{{-- Footer body --}}
@section('footer-body')
    
    {{-- Footer top widget area begin yield is required --}}
    @section('footer-area-top')
        @include('partials.sidebar', ['id' => 'footer-area-top', 'classes' => $footerAreaTopClasses ?? ['o-grid']])
    @stop
    @hasSection('footer-area-top')
        @slot('prefooter')
            @yield('footer-area-top')
        @endslot
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
    @section('footer-area-bottom')
        @include('partials.sidebar', ['id' => 'footer-area-bottom', 'classes' => $footerAreaBottomClasses ?? ['o-grid']])
    @stop
    @hasSection('footer-area-bottom')
        @slot('postfooter')
            @yield('footer-area-bottom')
        @endslot
    @endif
@show

{{-- After footer body --}}
@yield('after-footer-body')

@endfooter
