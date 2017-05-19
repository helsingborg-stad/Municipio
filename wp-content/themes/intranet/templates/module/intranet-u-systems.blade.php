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

    @if (!$module->hideTitle)
    <h4 class="box-title">
        <?php _e('My systems', 'municipio-intranet'); ?>

        @if (is_user_logged_in())
        <button type="button" onclick="location.hash='modal-select-systems'" class="btn btn-plain btn-sm pricon pricon-edit pricon-space-right" data-user-systems-edit><?php _e('Select systems', 'municipio-intranet'); ?></button>
        @endif
    </h4>
    @endif

    <ul class="links">
        @if (!is_user_logged_in())
            <li class="creamy text-sm" style="border:none;"><?php _e('You need to login to your account to access the systems list.', 'municipio-intranet'); ?></li>
        @else
            @if (empty($systems))
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

                @foreach ($systems as $system)
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

<div id="modal-select-systems" class="modal modal-backdrop-2 modal-small" tabindex="-1" role="dialog" aria-hidden="true">
    <form action="{{ municipio_intranet_current_url() }}" method="post">
        <?php wp_nonce_field('save', 'select-systems'); ?>

        <div class="modal-content">
            <div class="modal-header">
                <a class="btn btn-close" href="#close"></a>
                <h2 class="modal-title"><?php _e('Select systems', 'municipio-intranet'); ?></h2>
            </div>
            <div class="modal-body">
                <article>
                    <p>
                        <?php _e('Select the systems that you would like to show in your "My systems" area.', 'municipio-intranet'); ?>
                    </p>

                    <p>
                        <table class="table table-bordered table-va-top">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?php _e('Name', 'municipio-intranet'); ?></th>
                                    <th><?php _e('Description', 'municipio-intranet'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (\Intranet\User\Systems::getAvailabelSystems('user', array('user')) as $system)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="system-selected[]" value="{{ $system->id }}" {{ checked(true, $system->selected) }}>
                                    </td>
                                    <td>{{ $system->name }}</td>
                                    <td>{{ $system->description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </p>
                </article>
            </div>
            <div class="modal-footer">
                <a href="#close" class="btn btn-close"><?php _e('Cancel', 'municipio-intranet'); ?></a>
                <button type="submit" class="btn btn-primary"><?php _e('Save', 'municipio-intranet'); ?></button>
            </div>
        </div>
        <a href="#close" class="backdrop"></a>
    </form>
</div>
