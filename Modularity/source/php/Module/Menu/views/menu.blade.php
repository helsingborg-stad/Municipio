@if (!empty($menu['items']))
<div class="mod-menu {{'mod-menu--' . $displayAs}} {{$wrapped ? 'mod-menu--wrapped' : ''}}">
    @includeIf('menus.' . $displayAs . '.' . $displayAs)
</div>
@endif