@if (!empty($breadcrumbMenu['items']) || !empty($accessibilityMenu['items']))
    <div class="nav-helper @if (isset($classList)) {{ implode(' ', $classList) }} @endif" data-scope="s-nav-helper;">
        <div class="nav-helper__container">
            @includeIf('partials.navigation.breadcrumb')
            @includeIf('partials.navigation.accessibility')
        </div>
    </div>
@endif
