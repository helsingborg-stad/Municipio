@if (is_user_logged_in())
<div class="grid-sm-4 text-center-xs text-right-sm text-right-md text-right-lg">
    @if (!is_author() && get_blog_option(get_current_blog_id(), 'intranet_force_subscription') != 'true')
    {{ municipio_intranet_follow_button(get_current_blog_id()) }}
    @endif
</div>
@endif
