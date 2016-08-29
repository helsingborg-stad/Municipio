@if (isset($showLoginReminder) && $showLoginReminder)
<div id="modal-target" class="modal modal-backdrop-white modal-open modal-xs" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-content">
        <div class="box box-filled box-filled-red-4 box-filled-card pos-relative has-stripe">
            <div class="stripe stripe-red stripe-percentage">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>

            <div class="box-content pos-relative">
                <h2><?php _e('Employed in the city?', 'municipio-intranet'); ?></h2>
                <p>
                    <?php _e('If you are employed in the city of Helsingborg you can enahcne your intranet experience by logging in. Fill in your computer login information in the below form to login.', 'municipio-intranet'); ?>
                </p>

                <figure style="margin-bottom: -40px;">
                    <i class="pricon pricon-user pricon-5x pricon-badge pricon-badge-red-1"></i>
                    <i class="pricon pricon-discuss pricon-3x pricon-badge pricon-badge-red-2" style="position: relative;left:-20px;top: -20px;"></i>

                    {!! apply_filters('Municipio/footer_signature', '<a href="http://www.helsingborg.se"><img src="' . get_template_directory_uri() . '/assets/dist/images/helsingborg.svg" alt="Helsingborg Stad" class="footer-signature" style="width:80px;position: absolute;right: 20px;bottom: 20px;"></a>') !!}
                </figure>
            </div>
        </div>

        <div class="gutter gutter-xl" style="margin-top: -40px;">
            @include('partials.user.loginform')
        </div>

        <div class="creamy creamy-border-top gutter text-center">
            <a href="#close" data-action="modal-close" class="btn btn-block"><?php _e('Continue without logging in', 'municipio-intranet'); ?></a>
        </div>
    </div>

    <div class="backdrop"></div>
</div>
@endif
