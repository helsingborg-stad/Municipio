@if (!empty($breadcrumbItems) || !empty($accessibilityItems))
    <div class="nav-helper">
        @includeIf('partials.navigation.breadcrumb')
        @includeIf('partials.navigation.accessibility')
    </div>
@endif
