<?php
    global $searchFormNode;
    global $counts;

    $searchFormNode = ($searchFormNode) ? $searchFormNode+1 : 1;
    $searchLevel = isset($_GET['level']) && !empty($_GET['level']) ? sanitize_text_field($_GET['level']) : null;

    // Set default searchLevel variable if not set in qet params
    if (!$searchLevel) {
        if (is_main_site()) {
            if (is_user_logged_in()) {
                $searchLevel = 'subscriptions';
            } else {
                $searchLevel = 'all';
            }
        } else {
            $searchLevel = 'current';
        }
    }
?>
<div class="search" itemscope itemtype="http://schema.org/WebSite">
    <meta itemprop="url" content="{{ home_url() }}">

    <form method="get" action="{{ home_url() }}" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">
        <meta itemprop="target" content="{{ home_url('?s=') }}{search_term_string}">

        @if (is_front_page())
            <label class="label label-lg label-theme" for="searchkeyword-{{ $searchFormNode }}">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>
        @else
            <label for="searchkeyword-{{ $searchFormNode }}" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>
        @endif

        @if (is_search())
        <div class="form-group">
            <ul class="segmented-control">
                @if (is_user_logged_in())
                <li>
                    <input id="search-level-subscriptions" type="radio" name="level" value="subscriptions" {{ checked('subscriptions', $searchLevel) }}>
                    <label for="search-level-subscriptions" class="checkbox inline-block"><?php _e('Subscriptions', 'municipio-intranet'); ?></label>
                </li>
                @endif

                <li>
                    <input id="search-level-all" type="radio" name="level" value="all" {{ checked('all', $searchLevel) }}>
                    <label for="search-level-all" class="checkbox inline-block"><?php _e('All sites', 'municipio-intranet'); ?></label>
                </li>
                <li>
                    <input id="search-level-current" type="radio" name="level" value="current" {{ checked('current', $searchLevel) }}>
                    <label for="search-level-current" class="checkbox inline-block"><?php _e('Current site', 'municipio-intranet'); ?></label>
                </li>
            </ul>

            @if (is_user_logged_in())
            <ul class="segmented-control">
                <li>
                    <input id="search-level-users" type="radio" name="level" value="users" {{ checked('users', $searchLevel) }}>
                    <label for="search-level-users" class="checkbox inline-block">
                        <?php _e('Users', 'municipio-intranet'); ?>

                        @if (isset($counts['users']) && $counts['users'] > 0)
                        <span class="label label-sm label-theme label-rounded">{{ $counts['users'] }}</span>
                        @endif
                    </label>
                </li>
            </ul>
            @endif
        </div>
        @endif

        <div class="form-group input-group input-group-lg">
            <input itemprop="query-input" required id="searchkeyword-{{ $searchFormNode }}" autocomplete="off" class="form-control form-control-lg" type="search" name="s" placeholder="{{ get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : 'What are you looking for?' }}" value="<?php echo (!empty(get_search_query())) ? get_search_query() : ''; ?>">
            <span class="input-group-addon-btn">
                <input type="submit" class="btn btn-primary btn-lg" value="{{ get_field('search_button_text', 'option') ? get_field('search_button_text', 'option') : __('Search', 'municipio') }}">
            </span>
        </div>
    </form>
</div>
