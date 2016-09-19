<input type="hidden" name="post_author_override" value="<?php echo $post->post_author; ?>">

<div class="intranet-author-select-filter">
    <input type="text" placeholder="Sök" name="intranet-author-select-filter">
</div>
<ul class="intranet-author-select">
    <?php
    foreach ($authors as $author) :
    $selected = $post->post_author == $author->ID ? 'selected' : '';
    ?>
    <li class="<?php echo $selected; ?>" data-user-id="<?php echo $author->ID; ?>">
        <?php if (get_user_meta($author->ID, 'user_profile_picture', true)) : ?>
            <div class="profile-image" style="background-image:url('<?php echo get_field('user_profile_picture', 'user_' . $author->ID); ?>');"></div>
        <?php else : ?>
            <div class="profile-image"></div>
        <?php endif; ?>
        <div class="profile-info">
            <span class="user-fullname"><?php echo get_user_meta($author->ID, 'first_name', true); ?> <?php echo get_user_meta($author->ID, 'last_name', true); ?></span>
            <span class="user-login"><?php echo $author->data->user_login; ?></span>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
