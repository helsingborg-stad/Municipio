<form class="search" method="get" action="/">
    <label for="searchkeyword-mobile" class="sr-only"><?php echo get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio'); ?></label>

    <div class="input-group">
        <input id="searchkeyword-mobile" autocomplete="off" class="form-control" type="search" name="s" placeholder="<?php echo get_field('search_placeholder_text', 'option') ? get_field('search_placeholder_text', 'option') : __('What are you looking for?', 'municipio'); ?>" value="<?php echo (!empty(get_search_query())) ? get_search_query() : ''; ?>">
        <span class="input-group-addon-btn">
            <input type="submit" class="btn btn-primary" value="<?php echo get_field('search_label_text', 'option') ? get_field('search_label_text', 'option') : __('Search', 'municipio'); ?>">
        </span>
    </div>
</form>

{!! $navigation['mobileMenu'] !!}

<ul class="nav nav-mobile">
    <li class="subnav-icon"><a href="?walkthrough"><i class="pricon pricon-question-o pricon-lg"></i> <?php _e('Help', 'municipio-intranet'); ?></a></li>
    <li><a href="{{ municipio_table_of_contents_url() }}"><?php _e('A-Z', 'municipio-intranet'); ?></a></li>
</ul>

{!!
    wp_nav_menu(array(
        'theme_location' => 'help-menu',
        'container' => 'nav',
        'container_class' => 'menu-help',
        'container_id' => '',
        'menu_class' => 'nav nav-mobile',
        'menu_id' => 'help-menu-top',
        'echo' => 'echo',
        'before' => '',
        'after' => '',
        'link_before' => '',
        'link_after' => '',
        'items_wrap' => '<ul class="%2$s">%3$s</ul>',
        'depth' => 1,
        'fallback_cb' => '__return_false'
    ));
!!}
