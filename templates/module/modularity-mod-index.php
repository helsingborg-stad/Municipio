<?php
    global $post;
    $items = get_field('index', $module->ID);
?>
<div class="grid" data-equal-container>
    <?php foreach ($items as $item) : $post = $item['page']; setup_postdata($post); ?>
    <div class="grid-md-6">
        <a href="<?php the_permalink(); ?>" class="box box-index" data-equal-item>
            <?php if ($item['image_display'] == 'featured' && $thumbnail = get_thumbnail_source()) : ?>
                <img class="box-image" src="<?php echo $thumbnail; ?>">
            <?php elseif ($item['image_display'] == 'custom' && !empty($item['custom_image'])) : ?>
                <img class="box-image" src="<?php echo $item['custom_image']['url']; ?>" alt="<?php echo (!empty($item['custom_image']['alt'])) ? $item['custom_image']['alt'] : $item['custom_image']['description']; ?>">
            <?php endif; ?>

            <div class="box-content">
                <h5 class="box-index-title link-item"><?php the_title(); ?></h5>
                <?php the_excerpt(); ?>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
