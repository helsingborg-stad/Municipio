@if (is_user_logged_in())
<div class="grid-md-4 text-center-xs text-center-sm text-right-md text-right-lg">
    @if (!is_author() && get_blog_option(get_current_blog_id(), 'intranet_force_subscription') != 'true')
    <button class="btn btn-primary btn-subscribe" data-subscribe="{{ get_current_blog_id() }}">
        @if (!\Intranet\User\Subscription::hasSubscribed(get_current_blog_id()))
        <i class="pricon pricon-plus-o"></i> <?php _e('Follow', 'municipio-intranet'); ?>
        @else
        <i class="pricon pricon-minus-o"></i> <?php _e('Unfollow', 'municipio-intranet'); ?>
        @endif
    </button>
    @endif
</div>
@endif
