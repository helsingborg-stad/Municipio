@if (is_active_sidebar('bottom-sidebar'))
    <?php dynamic_sidebar('bottom-sidebar'); ?>
@endif

@footer([
    'logotype' => $logotype->negative['url'],
    'logotypeHref' => $homeUrl
])
@endfooter