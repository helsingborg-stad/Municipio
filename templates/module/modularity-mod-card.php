<?php
    global $post;
    $cards = get_field('card', $module->ID);
?>
<div class="grid" data-equal-container>
    <?php foreach ($cards as $card) : $post = $card['page']; setup_postdata($post); ?>
    <div class="grid-md-6">
        <a href="<?php the_permalink(); ?>" class="box box-index" data-equal-item>
            <?php if ($thumbnail = get_thumbnail_source()) : ?>
            <img class="box-image" src="<?php echo $thumbnail; ?>">
            <?php endif; ?>

            <div class="box-content">
                <h5 class="box-index-title link-item"><?php the_title(); ?></h5>
                <?php the_excerpt(); ?>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
