<div id="modal-missing-info" class="modal modal-backdrop-4 modal-small {{ $show_userdata_guide ? 'modal-open' : '' }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title"><?php _e('We\'re missing some information about you', 'municipio-intranet'); ?></h2>

            @if (!$show_userdata_guide)
            <a href="#close" class="modal-close btn btn-close" title="<?php _e('Close', 'municipio-intranet'); ?>"></a>
            @endif
        </div>
        <div class="modal-body">
            <div class="grid">
                <div class="grid-xs-12 no-padding">
                    <form action="" method="post">
                        {!! wp_nonce_field('user_missing_data_' . get_current_user_id()) !!}
                        <input type="hidden" name="user_missing_data" value="true">

                        <div class="accordion accordion-list">
                            <section class="accordion-section">
                                <input type="radio" name="active-section" id="accordion-missing-instruction" checked>
                                <span class="accordion-toggle">
                                    <h3><?php _e('Instructions', 'municipio-intranet'); ?></h3>
                                </span>
                                <div class="accordion-content">
                                    <div class="grid">
                                        <div class="grid-md-12">
                                            <article class="no-padding">
                                                <?php if ($show_userdata_guide) : ?>
                                                <p><?php _e('Some vital information is missing from your profile. Before you can fully use the intranet we will need you to complete this guide to do the final setup.', 'municipio-intranet'); ?></p>
                                                <p><?php _e('Click "next" to continue.', 'municipio-intranet'); ?></p>
                                                <?php else : ?>
                                                <p><?php _e('Your profile is not complete. Please go through the steps in this guide to update your information.', 'municipio-intranet'); ?></p>
                                                <p><?php _e('Click "next" to continue.', 'municipio-intranet'); ?></p>
                                                <?php endif; ?>
                                            </article>
                                        </div>
                                    </div>

                                    <div class="accordion-nav clearfix">
                                        <label class="btn btn-md btn-primary pull-right" data-guide-nav="next" for="accordion-missing-1"><?php _e('Next', 'municipio-intranet'); ?> <i class="fa fa-caret-right"></i></label>
                                    </div>
                                </div>
                            </section>

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

    @if (!$show_userdata_guide)
    <a href="#" class="backdrop"></a>
    @else
    <div class="backdrop"></div>
    @endif
</div>
