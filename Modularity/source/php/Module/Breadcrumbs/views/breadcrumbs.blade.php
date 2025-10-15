@if (!empty($breadcrumbItems) || !empty($accessibilityItems))
    <div class="nav-helper o-container">
        @includeIf('partials.breadcrumb')
        @includeIf('partials.accessibility')
    </div>
@endif
