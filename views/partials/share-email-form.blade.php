<div id="modal-target-{{ get_the_ID() }}" class="modal modal-backdrop-2 modal-xs text-left" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-content material-shadow-lg">
        <form class="social-share-email">
            <div class="modal-header">
                <a class="btn btn-close" href="#close"></a>
                <h2 class="modal-title"><?php _e('Share as e-mail', 'municipio'); ?></h2>
            </div>
            <div class="modal-body">
                <article>
                    <div class="form-group">
                        <label>URL</label>
                        <a href="{{ get_the_permalink() }}" target="_blank">{{ get_the_permalink() }}</a>
                    </div>
                    @if (!is_user_logged_in())
                        <div class="form-group">
                            <label for="sender-name"><?php _e('Your name', 'municipio'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="sender_name" id="sender-name" placeholder="<?php _e('Your name', 'municipio'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="sender-email"><?php _e('Your email', 'municipio'); ?> <span class="text-danger">*</span></label>
                            <input type="email" name="sender_email" id="sender-email" placeholder="<?php _e('Your email', 'municipio'); ?>" required>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="recipient-email"><?php _e('Recipient email', 'municipio'); ?> <span class="text-danger">*</span></label>
                        <input type="email" name="recipient_email" id="recipient-email" placeholder="<?php _e('Recipient email', 'municipio'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="message"><?php _e('Message', 'municipio'); ?></label>
                        <textarea name="message" id="message" rows="4" placeholder="<?php _e('Message', 'municipio'); ?>"></textarea>
                    </div>
                    @if (!is_user_logged_in())
                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="{{ $g_recaptcha_key }}"></div>
                        </div>
                    @endif
                </article>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="post_id" value="{{ the_ID() }}">
                <input type="submit" class="btn btn-primary" value="<?php _e('Send', 'municipio'); ?>">
            </div>
        </form>
    </div>
    <a href="#close" class="backdrop"></a>
</div>