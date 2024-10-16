@siteselector([
  'classList' => ['u-display--none@xs', 'u-display--none@sm', 'u-display--none@md'],
  'items' => $siteselectorMenu['items'],
  'maxItems' => $customizer->siteSelectorMaxItems ?? 3
])
@endsiteselector