{{-- Above header --}}
@section('above-header')
    @include('partials.navigation.search-top')
@show

<header id="site-header" class="{{ apply_filters('Views/Partials/Header/HeaderClass', $headerLayout['classes']) }}">
    {{-- Before header body --}}
    @yield('before-header-body')

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
