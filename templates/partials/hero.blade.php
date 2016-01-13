<div class="hero hidden-xs hidden-sm" style="background-image:url('http://www.helsingborg.se/wp-content/uploads/2015/11/skolflicka_i_kattarp_1800x600_foto_anna_alexander_olsson.jpg');">
    @if (is_front_page())
        @include('templates.partials.stripe');
    @endif

    <?php get_search_form(); ?>
</div>
