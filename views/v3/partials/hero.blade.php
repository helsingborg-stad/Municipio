@if (is_active_sidebar('slider-area') === true )
    <div class="hero has-stripe sidebar-slider-area">
        <div class="grid">
            <?php dynamic_sidebar('slider-area'); ?>
        </div>

        @include('partials.stripe')

        @if (rtrim($_SERVER['REQUEST_URI'], "/") == "" && is_array(get_field('search_display', 'option')) && in_array('hero', get_field('search_display', 'option')))
            {{ get_search_form() }}
        @endif
    </div>
@endif
