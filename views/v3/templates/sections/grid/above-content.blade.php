{{-- Content notices --}}
@include('templates.sections.content-notices', [
    'classes' => []
])

@yield('above-before')

@section('above')
    @include('partials.sidebar', ['id' => 'above-columns-sidebar', 'classes' => ['o-layout-grid--col-span-12']])
@show

@yield('above-after')