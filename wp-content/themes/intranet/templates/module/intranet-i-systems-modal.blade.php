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
                                @foreach ($availableSystems as $system)
                                <?php $tooltip = $system->forced ? 'data-tooltip="' . __('Mandatory', 'municipio-intranet') . '" data-tooltip-top' : ''; ?>
                                <tr>
                                    <td class="text-center" {!! $tooltip !!}>
                                        <input type="checkbox" name="system-selected[]" value="{{ $system->id }}" {{ checked(true, $system->selected) }} {{ $system->forced ? 'disabled' : '' }}>
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
