<div class="box box-panel">
    <?php
        echo municipio_intranet_walkthrough(
            __('My links', 'municipio-intranet'),
            __('Here you can add links to websites that you usally use or want to be able to reach quickly. The links can be both pages on the intranet and pages on other websites (like social media websites or other work tools) that you need a quick link to.', 'municipio-intranet'),
            '.modularity-mod-intranet-u-links',
            'top-left',
            'right'
        );
    ?>

    <?php if (!$module->hideTitle) : ?>
    <h4 class="box-title">
        <?php _e('My links', 'municipio-intranet'); ?>
        <?php if (is_user_logged_in()) : ?>
        <button type="button" class="btn btn-plain btn-sm pricon-space-right pricon pricon-edit" data-user-link-edit><?php _e('Edit', 'municipio-intranet'); ?></button>
        <?php endif; ?>
    </h4>
    <?php endif; ?>

    <?php if (!empty(\Intranet\Module\UserLinks::getLinks())) : ?>
    <ul class="links">
        <?php foreach (\Intranet\Module\UserLinks::getLinks() as $link) : ?>
        <li>
            <a target="_blank" class="link-item" href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a>
            <?php if (is_user_logged_in()) : ?>
            <button class="btn btn-icon btn-sm text-lg pull-right only-if-editing" data-user-link-remove="<?php echo $link['url']; ?>" type="button" data-tooltip="<?php _e('Remove'); ?>">&times;</button>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else : ?>
        <div class="box-content"><?php _e('You have not added any links yet…', 'municipio-intranet'); ?></div>
    <?php endif; ?>

    <?php if (is_user_logged_in()) : ?>
    <form action="<?php echo municipio_intranet_current_url(); ?>" class="only-if-editing" data-user-link-add>
        <h5><?php _e('Add new link', 'municipio-intranet'); ?></h5>
        <div class="form-group">
            <label for="user-link-title"><?php _e('Title', 'municipio-intranet'); ?></label>
            <input type="text" name="user-link-title" id="user-link-title" title="Ange länk-titel" required>
        </div>
        <div class="form-group">
            <label for="user-link-url"><?php _e('Url', 'municipio-intranet'); ?></label>
            <input type="text" name="user-link-url" id="user-link-url" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn"><?php _e('Save'); ?></button>
        </div>
    </form>
    <?php endif; ?>
</div>
