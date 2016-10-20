@if ($hasLeftSidebar)
<aside class="grid-md-4 grid-lg-3 sidebar-left-sidebar hidden-print">
    @if (is_active_sidebar('left-sidebar'))
        <div class="grid sidebar-left-sidebar-top">
            <?php dynamic_sidebar('left-sidebar'); ?>
        </div>
    @endif

    @if (get_field('nav_sub_enable', 'option'))
    {!! $navigation['sidebarMenu'] !!}
    @endif

    @if (is_main_site() && is_front_page() && $forced = \Intranet\User\Subscription::getForcedSubscriptions(false, false))
    <ul>
    @foreach ($forced as $site)
        <li class="link-box network-title"><a href="{{ $site->path }}">{!! municipio_intranet_format_site_name($site) !!}</a></li>
    @endforeach
    </ul>
    @endif

    @if (is_active_sidebar('left-sidebar-bottom'))
        <div class="grid sidebar-left-sidebar-bottom">
            <?php dynamic_sidebar('left-sidebar-bottom'); ?>
        </div>
    @endif
</aside>
@endif
