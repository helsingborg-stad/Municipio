@if (!empty($breadcrumbMenu['items']) || !empty($accessibilityMenu['items']))
    <div class="nav-helper @if (isset($classList)) {{ implode(' ', $classList) }} @endif">
        @includeIf('partials.navigation.breadcrumb')
        @includeIf('partials.navigation.accessibility')
    </div>
@endif
