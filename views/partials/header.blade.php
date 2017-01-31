<header id="site-header" class="site-header {{ $headerLayout['class'] }} {{ is_front_page() && get_field('header_transparent', 'option') ? 'header-transparent' : '' }} {{ get_field('header_centered', 'option') ? 'header-center' : '' }}">
    <div class="print-only container">
        <div class="grid">
            <div class="grid-sm-12">
                {!! municipio_get_logotype('standard') !!}
            </div>
        </div>
    </div>

    @include('partials.header.' . $headerLayout['template'])
</header>

@include('partials.hero')
