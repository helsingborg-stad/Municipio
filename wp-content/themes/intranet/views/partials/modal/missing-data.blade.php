<div id="modal-hej" class="modal modal-open modal-backdrop-4 modal-small" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title"><?php _e('We\'re missing some information about you', 'municipio-intranet'); ?></h2>
        </div>
        <div class="modal-body">
            <div class="grid">
                <div class="grid-xs-12 no-padding">
                    <form action="" method="post">
                        {!! wp_nonce_field('user_missing_data_' . $user->ID) !!}
                        <input type="hidden" name="user_missing_data" value="true">

                        <div class="accordion accordion-list">
                            <?php $i = 1; ?>
                            @foreach ($missing as $item)
                                @include('partials.modal.missing-data-fields.' . $item)
                                <?php $i++; ?>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <a href="#" class="backdrop"></a>
</div>
