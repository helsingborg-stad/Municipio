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

    @if (!$hideTitle && !empty($post_title))
    <h4 class="box-title">
        <?php _e('My links', 'municipio-intranet'); ?>

        @if (is_user_logged_in())
        <button type="button" class="btn btn-plain btn-sm pricon-space-right pricon pricon-edit" data-user-link-edit><?php _e('Edit', 'municipio-intranet'); ?></button>
        @endif
    </h4>
    @endif

    @if (!empty($links))
    <ul class="links">
        @foreach ($links as $link)
        <li>
            <a target="_blank" class="link-item" href="{{ $link['url'] }}">{{ $link['title'] }}</a>
            @if (is_user_logged_in())
            <button class="btn btn-icon btn-sm text-lg pull-right only-if-editing" data-user-link-remove="{{ $link['url'] }}" type="button" data-tooltip="<?php _e('Remove'); ?>">&times;</button>
            @endif
        </li>
        @endforeach
    </ul>
    @else
        <div class="box-content"><?php _e('You have not added any links yet…', 'municipio-intranet'); ?></div>
    @endif

    @if (is_user_logged_in())
    <form action="{{ municipio_intranet_current_url() }}" class="only-if-editing" data-user-link-add>
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
    @endif
</div>
