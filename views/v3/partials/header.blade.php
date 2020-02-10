{{-- Above header --}}
@section('above-header')
@stop

<header id="site-header" class="{{ apply_filters('Views/Partials/Header/HeaderClass', $headerLayout['classes']) }}">
    {{-- Before header body --}}
    @section('before-header-body')
        @include('partials.navigation.search-top')
    @show

    {{-- Header body --}}
    @yield('header-body')

    {{-- After header body --}}
    @yield('after-header-body')
</header>

{{-- Below header --}}
@section('below-header')
    @include('partials.hero')

    @if (is_active_sidebar('top-sidebar'))
        <?php dynamic_sidebar('top-sidebar'); ?>
    @endif
@show
