<div class="box box-panel">
    <?php
        echo municipio_intranet_walkthrough(
            __('My systems', 'municipio-intranet'),
            __('This is a list of the most common systems in your administration unit. Click the edit button to add more systems to the list.', 'municipio-intranet'),
            '.modularity-mod-intranet-u-syste',
            'top-left',
            'right'
        );
    ?>

    @if (!$hideTitle && !empty($post_title))
    <h4 class="box-title">
        <?php _e('My systems', 'municipio-intranet'); ?>

        @if (is_user_logged_in())
        <button type="button" onclick="location.hash='modal-select-systems'" class="btn btn-plain btn-sm pricon pricon-edit pricon-space-right" data-user-systems-edit><?php _e('Select systems', 'municipio-intranet'); ?></button>
        @endif
    </h4>
    @endif

    @if (isset($_GET['save-system']) && $_GET['save-system'] == "error")
    <div class="notice danger">
        <i class="pricon pricon-notice-warning"></i> <?php _e("Could not save your selected systems.", 'municipio-intranet'); ?>
    </div>
    @endif

    @if (isset($_GET['save-system']) && $_GET['save-system'] == "saved")
    <div class="notice success">
        <i class="pricon pricon-check"></i> <?php _e("Your systems list has been updated.", 'municipio-intranet'); ?>
    </div>
    @endif

    <ul class="links">
        @if (!is_user_logged_in())
            <li class="creamy text-sm" style="border:none;"><?php _e('You need to login to your account to access the systems list.', 'municipio-intranet'); ?></li>
        @else
            @if (empty($selectedSystems))
                <li class="text-center">
                    <div class="gutter">
                        <?php _e('You have not selected any systems to display.', 'municipio-intranet'); ?><br>
                        <button type="button" style="margin-top:7px;" onclick="location.hash='modal-select-systems'" class="btn btn-primary btn-md" data-user-systems-edit><?php _e('Select systems', 'municipio-intranet'); ?></button>
                    </div>
                </li>
            @else
                @if (method_exists('\SsoAvailability\SsoAvailability', 'isSsoAvailable') && !\SsoAvailability\SsoAvailability::isSsoAvailable())
                <li class="creamy text-sm" style="border:none;"><?php _e('Your logged in from a computer outside the city network. This causes some systems to be unavailable.', 'municipio-intranet'); ?></li>
                @endif

                @foreach ($selectedSystems as $system)
                    @if ($system->unavailable === true)
                    <li><a target="_blank" class="link-item link-unavailable" href="{{ $system->url }}"><span data-tooltip="<?php _e('You need to be on the city network to use this system', 'municipio-intranet'); ?>">{{ $system->name }}</span></a></li>
                    @else
                    <li><a target="_blank" href="{{ $system->url }}" class="link-item">{{ $system->name }}</a></li>
                    @endif
                @endforeach
            @endif
        @endif
    </ul>
</div>
