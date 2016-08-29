<div id="forgot-password" class="modal modal-backdrop-2 modal-small" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <a class="btn btn-close" href="#close"></a>
            <h2 class="modal-title"><?php _e('Forgot your password?', 'municipio-intranet'); ?></h2>
        </div>
        <div class="modal-body">
            <article>
                {!! get_site_option('password-reset-instructions') !!}
            </article>
        </div>
        <div class="modal-footer">
            <a href="#close" class="btn btn-default"><?php _e('Close', 'municipio-intranet'); ?></a>
        </div>
    </div>
    <a href="#close" class="backdrop"></a>
</div>
