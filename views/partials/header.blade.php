<header id="site-header" class="site-header {{ $headerLayout['classes'] }}">
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

@if (is_active_sidebar('top-sidebar'))
    <?php dynamic_sidebar('top-sidebar'); ?>
@endif
