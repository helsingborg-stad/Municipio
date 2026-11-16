@if(!empty($siteselectorMenu['items']))
    @siteselector([
        'classList' => $menuItem->getCssClasses(),
        'items' => $siteselectorMenu['items'],
        'maxItems' => $customizer->siteSelectorMaxItems ?? 3
    ])
    @endsiteselector
@endif