{{-- Above footer --}}
@yield('above-footer')

<footer id="site-footer" class="{{ apply_filters('Views/Partials/Header/FooterClass', $footerLayout['classes']) }}">
    {{-- Before footer body --}}
    @yield('before-footer-body')

    {{-- Footer body --}}
    @yield('footer-body')

    {{-- After footer body --}}
    @yield('after-footer-body')
</footer>

{{-- Below header --}}
@yield('below-footer')
