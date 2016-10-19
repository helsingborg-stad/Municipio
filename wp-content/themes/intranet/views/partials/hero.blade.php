@if (is_active_sidebar('slider-area') === true )
    <div class="hero sidebar-slider-area">
        <div class="grid no-gutter">
            <?php dynamic_sidebar('slider-area'); ?>
        </div>

        @if (is_front_page() && is_array(get_field('search_display', 'option')) && in_array('hero', get_field('search_display', 'option')))
            {{ get_search_form() }}
        @endif
    </div>
@endif
