@scope(['name' => ['archive-pagination', $postType . '-archive-pagination']])
    @pagination([
        ...$getPaginationComponentArguments(),
        'classList' => ['u-margin__top--8', 'u-display--flex', 'u-justify-content--center'],
    ])
    @endpagination
@endscope