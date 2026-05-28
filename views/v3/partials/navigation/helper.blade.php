@if (!empty($breadcrumbMenu['items']) || !empty($accessibilityMenu['items']))
    @scope(['name' => ['nav-helper']])
        <div class="nav-helper @if (isset($classList)) {{ implode(' ', $classList) }} @endif">
            <div class="nav-helper__container">
                @includeIf('partials.navigation.breadcrumb')
                @includeIf('partials.navigation.accessibility')
            </div>
        </div>
    @endscope
@endif
