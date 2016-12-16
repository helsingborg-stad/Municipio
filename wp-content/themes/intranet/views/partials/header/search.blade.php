<form class="search search-main hidden-xs hidden-sm" method="get" action="{{ home_url() }}">
    <label for="searchkeyword-top" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>

    <?php $searchLevel = isset($_GET['level']) && !empty($_GET['level']) ? sanitize_text_field($_GET['level']) : null; ?>
    @if ($searchLevel)
    <input type="hidden" name="level" value="{{ $searchLevel }}">
    @endif

    <div class="input-group">
        <input id="searchkeyword-top" autocomplete="off" class="form-control" type="search" name="s" placeholder="<?php is_user_logged_in() ? _e('Search for content, files and staff', 'municipio-intranet') : _e('Search for content and files', 'municipio-intranet'); ?>…" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? urldecode(stripslashes($_GET['s'])) : ''; ?>" required>
        <span class="input-group-addon-btn">
            <button type="submit" class="btn"><?php _e('Search', 'municipio'); ?></button>
        </span>
    </div>

    <?php
        echo municipio_intranet_walkthrough(
            __('Search', 'municipio-intranet'),
            __('Type what you are looking for. You will get search results from all intranets. If you are logged in you can also search collegues by searching.', 'municipio-intranet'),
            '.search-main'
        );
    ?>
</form>
