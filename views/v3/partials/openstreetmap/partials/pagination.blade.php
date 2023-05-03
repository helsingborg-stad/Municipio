@pagination([
    'list' => [
        ['href' => '?pagination=1', 'label' => 'Page 1'],
    ],
    'classList' => [
        'u-padding__top--8',
        'u-padding__bottom--6',
        'u-justify-content--center'
    ],
    'useJS' => true,
    'current' => 1,
    'perPage' => $perPage,
    'pagesToShow' => 4,
])
@endpagination