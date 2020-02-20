{{-- Above header --}}
@section('above-header')
@stop

<header id="site-header" class="{{ $headerLayout['classes'] }}">
    {{-- Before header body --}}
    @section('before-header-body')
        @includeIf('partials.navigation.search-top')
    @show

    {{-- Header body --}}
    @yield('header-body')

    {{-- TODO: Move class to controller --}}
    @navbar(['items' => \Municipio\Helper\Nav::getTopLevel()])
    @endnavbar

    {{-- After header body --}}
    @yield('after-header-body')
</header>

{{-- Below header --}}
@section('below-header')
    @includeIf('partials.hero')

    @if (is_active_sidebar('top-sidebar'))
        <?php dynamic_sidebar('top-sidebar'); ?>

    @endif
@show
