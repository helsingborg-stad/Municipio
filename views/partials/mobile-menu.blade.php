<form class="search" method="get" action="/">
    <label for="searchkeyword-mobile" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : 'Search' }}</label>

    <div class="input-group">
        <input id="searchkeyword-mobile" autocomplete="off" class="form-control" type="search" name="s" placeholder="{{ get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : 'What are you looking for?' }}" value="<?php echo (!empty(get_search_query())) ? get_search_query() : ''; ?>">
        <span class="input-group-addon-btn">
            <input type="submit" class="btn btn-primary" value="{{ get_field('search_button_text', 'option') ? get_field('search_button_text', 'option') : 'Search' }}">
        </span>
    </div>
</form>


{!! $navigation['mobileMenu'] !!}
