<nav aria-label="{{ $lang->primaryNavigation }}" 
    id="main-menu"
    class="{{
        implode(',', $primaryMenuClassList ?? 
            [
                'u-display--none@xs', 
                'u-display--none@sm', 
                'u-display--none@md', 
                'u-print-display--none'
    ])}}"
>
    @nav([
        'id' => 'menu-primary',
        'items' => $primaryMenuItems,
        'allowStyle' => true,
        'direction' => 'horizontal',
        'classList' => array_merge(
            (array) $classList,
            ['s-nav-primary']
        ),
        'context' => ['site.header.nav', 'site.header.casual.nav'],
        'height' => 'lg',
        'expandLabel' => $lang->expand,
        'includeToggle' => $customizer->primaryMenuDropdown ?? false
    ])
    @endnav
</nav>