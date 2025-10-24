{{-- Content notices --}}
@include('templates.sections.content-notices', [
    'classes' => []
])

{{-- Expired event notice --}}
@include('partials.schema.event.expired-notice', [
    'classes' => []
])

@section('above')
    @include('partials.sidebar', ['id' => 'above-columns-sidebar', 'classes' => ['o-layout-grid--col-span-12']])
@show

@yield('above-main')