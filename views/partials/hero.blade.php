<div class="hero hidden-xs hidden-sm">
    @if (is_active_sidebar('slider-area'))
        {{ dynamic_sidebar('slider-area') }}
    @endif

    @include('views.partials.stripe')

    @if (is_front_page())
        {{ get_search_form() }}
    @endif

</div>
