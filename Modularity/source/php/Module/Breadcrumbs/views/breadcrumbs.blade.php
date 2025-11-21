@if (!empty($breadcrumbItems) || !empty($accessibilityItems))
    @element([
        'classList' => $classList
    ])
        @includeIf('partials.breadcrumb')
        @includeIf('partials.accessibility')
    @endelement
@endif
