@siteselector([
  'classList' => ['u-display--none@xs', 'u-display--none@sm', 'u-display--none@md'],
  'items' => $siteselectorMenuItems,
  'maxItems' => $customizer->siteSelectorMaxItems ?? 3
])
@endsiteselector