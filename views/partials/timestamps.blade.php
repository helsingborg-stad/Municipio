@if (get_the_modified_time() != get_the_time())

    @if(!is_null(get_field('show_date_updated','option')) && !is_null(get_field('show_date_published','option')) && !in_array(get_post_type(get_the_id()), get_field('show_date_updated','option')) && !in_array(get_post_type(get_the_id()), get_field('show_date_published','option')))
        <ul class="article-timestamps">

            @if(!is_null(get_field('show_date_published','option')) && in_array(get_post_type(get_the_id()),get_field('show_date_published','option')))
            <li>
                <strong>Publicerad:</strong>
                <time datetime="<?php echo the_time('Y-m-d H:i'); ?>">
                    <?php the_time('j F Y'); ?> kl. <?php the_time('H:i'); ?>
                </time>
            </li>
            @endif

            @if(!is_null(get_field('show_date_updated','option')) && in_array(get_post_type(get_the_id()), get_field('show_date_updated','option')))
            <li>
                <strong>Senast ändrad:</strong>
                <time datetime="<?php echo the_modified_time('Y-m-d H:i'); ?>">
                    <?php the_modified_time('j F Y'); ?> kl. <?php the_modified_time('H:i'); ?>
                </time>
            </li>
            @endif

        </ul>
    @endif

@else

    @if(!is_null(get_field('show_date_published','option')) && in_array(get_post_type(get_the_id()), get_field('show_date_published','option')))
        <ul class="article-timestamps">
            <li>
                <strong>Publicerad:</strong>
                <time datetime="<?php echo the_time('Y-m-d H:i'); ?>">
                    <?php the_time('j F Y'); ?> kl <?php the_time('H:i'); ?>
                </time>
            </li>
        </ul>
    @endif

@endif
