@if (is_active_sidebar('slider-area') === true )
    <div class="hero has-stripe hidden-xs hidden-sm">
        <?php dynamic_sidebar('slider-area'); ?>

        @include('partials.stripe')

        @if (is_front_page() && get_field('front_page_hero_search', 'option') === true)
            {{ get_search_form() }}
        @endif
    </div>
@endif
