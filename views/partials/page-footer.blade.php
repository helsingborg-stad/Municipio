<footer class="grid">
    <div class="grid-md-6">
        @if (get_the_modified_time() != get_the_time())
        <ul class="article-timestamps">
            <li>
                <strong>Publicerad:</strong>
                <time datetime="<?php echo the_time('Y-m-d H:i'); ?>">
                    <?php the_time('j F Y'); ?> kl. <?php the_time('H:i'); ?>
                </time>
            </li>
            <li>
                <strong>Senast ändrad:</strong>
                <time datetime="<?php echo the_modified_time('Y-m-d H:i'); ?>">
                    <?php the_modified_time('j F Y'); ?> kl. <?php the_modified_time('H:i'); ?>
                </time>
            </li>
        </ul>
        @else
        <ul class="article-timestamps">
            <li>
                <strong>Publicerad:</strong>
                <time datetime="<?php echo the_time('Y-m-d H:i'); ?>">
                    <?php the_time('j F Y'); ?> kl <?php the_time('H:i'); ?>
                </time>
            </li>
        </ul>
        @endif
    </div>

    <div class="grid-md-6 text-right">
        @if (get_field('show_share', get_the_id()) != 'false')
            @include('partials.social-share')
        @endif
    </div>
</footer>
