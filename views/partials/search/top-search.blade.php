<?php
    global $searchFormNode;
    $searchFormNode = ($searchFormNode) ? $searchFormNode+1 : 1;
?>
<form class="search top-search hidden-xs hidden-sm" method="get" action="{{ home_url(); }}">
    <label for="searchkeyword-{{ $searchFormNode }}" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>
    <div class="input-group">
        <input id="searchkeyword-{{ $searchFormNode }}" autocomplete="off" class="form-control" type="search" name="s" placeholder="{{ get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : 'What are you looking for?' }}" value="<?php echo (!empty(get_search_query())) ? get_search_query() : ''; ?>">
        <span class="input-group-addon-btn">
            <input type="submit" class="btn btn-primary" value="{{ get_field('search_button_text', 'option') ? get_field('search_button_text', 'option') : __('Search', 'municipio') }}">
        </span>
    </div>
</form>
