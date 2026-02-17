<!DOCTYPE html>
<html {!! $languageAttributes !!}>

@include('templates.sections.head')

{{-- Content --}}
@section('body-content')
    @include('templates.sections.banner-notices', [
        'classes' => []
    ])

    {{-- Site banner --}}
    @include('templates.sections.site-banner', [
        'classes' => []
    ])

    {{-- Site header --}}
    @include('templates.sections.site-header', [
        'classes' => []
    ])

    @includeWhen($helperNavBeforeContent, 'partials.navigation.helper', [
        'classList' => ['o-container', 'o-container--helper-nav'],
    ])

    {{-- Hero area and top sidebar --}}
    @section('hero-top-sidebar')
        @includeIf('partials.hero', ['classes' => [], 'sliderAreaClasses' => []])
        @includeIf('partials.sidebar', ['id' => 'top-sidebar', 'classes' => []])
    @show

    {{-- Layout --}}
    @include('templates.sections.grid.layout', $layoutData ?? [])

    @section('below')
        @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => []])
    @stop

    @include('templates.sections.bottom-sidebar')

    @section('footer')
        @includeIf('partials.footer')
    @show

    {{-- Floating menu --}}
    @include('partials.navigation.floating')

    {{-- Toast notices --}}
    @include('templates.sections.toast-notices')

    {{-- Wordpress required call to wp_footer() --}}
    {!! $wpFooter !!}
@stop


{{-- Including body --}}
@include('templates.sections.body', [
    'classes' => array_merge(
        explode(' ', $bodyClass),
        [
            'o-layout-grid',
            'o-layout-grid--cols-1',
        ]
    ),
])
</html>