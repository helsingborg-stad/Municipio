<form class="search search-main hidden-xs hidden-sm" method="get" action="{{ home_url() }}">
    <label for="searchkeyword-top" class="sr-only">{{ get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio') }}</label>

    <div class="input-group">
        <input id="searchkeyword-top" autocomplete="off" class="form-control" type="search" name="s" placeholder="<?php _e('Search for content, files and staff', 'municipio-intranet') ?>…" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? urldecode(stripslashes($_GET['s'])) : ''; ?>" required>
        <span class="input-group-addon-btn">
            <button type="submit" class="btn"><?php _e('Search', 'municipio'); ?></button>
        </span>
    </div>

    <?php
        echo municipio_intranet_walkthrough(
            __('Search', 'municipio-intranet'),
            __('Type what you are looking for and click to magnifying glass to search. You will get results from all of the city\'s intranets. You can search for information, documents or collegues. You can search a persons name, skills or tasks (only works if you are logged in).', 'municipio-intranet'),
            '.search-main'
        );
    ?>
</form>
