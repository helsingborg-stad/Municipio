<!DOCTYPE html>
<html {!! $languageAttributes !!}>

@include('templates.sections.head')

<body class="{{ $bodyClass }}" data-js-page-id="{{ $pageID }}" data-js-post-type="{{ $postType }}"
    @if ($customizer->headerSticky === 'sticky' && empty($headerData['nonStickyMegaMenu'])) data-js-toggle-item="mega-menu"
            data-js-toggle-class="mega-menu-open" @endif>
    <div class="site-wrapper">
        {{-- Banner Notices --}}
        @include('templates.sections.banner-notices')

        {{-- Site banner --}}
        @include('templates.sections.site-banner')

        {{-- Site header --}}
        @include('templates.sections.site-header')

        {{-- Helper navigation --}}
        @include('templates.sections.helper-nav')

        {{-- Hero area and top sidebar --}}
        @hasSection('hero-top-sidebar')
            @yield('hero-top-sidebar')
        @endif

        {{-- Before page layout --}}
        @section('before-layout')
        @show

        {{-- Notices before content --}}
        @include('templates.sections.content-notices')

        {{-- Page layout --}}
        <main id="main-content">
            @include('templates.sections.master.layout')
        </main>

        {{-- After page layout --}}
        @yield('after-layout')
    </div>

    @section('footer')
        @includeIf('partials.footer')
    @show

    {{-- Floating menu --}}
    @includeWhen(!empty($floatingMenu['items']), 'partials.navigation.floating')

    {{-- Notices Notice::add() --}}
    {{-- Shows up in the bottom left corner as toast messages --}}
    @include('templates.sections.toast-notices')
            
    {{-- Wordpress required call to wp_footer() --}}
    {!! $wpFooter !!}
    @show
</body>

</html>