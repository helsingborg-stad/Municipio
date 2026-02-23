{{-- Content notices --}}
@include('templates.sections.content-notices', [
    'classes' => []
])

@section('above')
    @include('partials.sidebar', ['id' => 'above-columns-sidebar', 'classes' => ['o-layout-grid--col-span-1']])
@show

@yield('above-main')