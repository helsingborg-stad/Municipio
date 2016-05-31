<?php
    global $searchFormNode;
    $searchFormNode = ($searchFormNode) ? $searchFormNode+1 : 1;
?>
<div class="search" itemscope itemtype="http://schema.org/WebSite">
    <meta itemprop="url" content="{{ home_url() }}">

    <form method="get" action="/" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">
        <meta itemprop="target" content="{{ home_url('?s=') }}{search_term_string}">

        @if (is_front_page())
            <label class="label label-lg label-theme" for="searchkeyword-{{ $searchFormNode }}">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>
        @else
            <label for="searchkeyword-{{ $searchFormNode }}" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>
        @endif

        <div class="input-group input-group-lg">
            <input itemprop="query-input" required id="searchkeyword-{{ $searchFormNode }}" autocomplete="off" class="form-control form-control-lg" type="search" name="s" placeholder="{{ get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : 'What are you looking for?' }}" value="<?php echo (!empty(get_search_query())) ? get_search_query() : ''; ?>">
            <span class="input-group-addon-btn">
                <input type="submit" class="btn btn-primary btn-lg" value="{{ get_field('search_button_text', 'option') ? get_field('search_button_text', 'option') : __('Search', 'municipio') }}">
            </span>
        </div>
    </form>
</div>
