@includeWhen($showSavedNotice, 'partials.success-notice')
@card([
    'classList' => [
        'kulturkortet-profile-editor',
        'o-layout-grid',
    ],
])
    @slot('aboveContent')
        @include('partials.title')
        @include('partials.form')
    @endslot
@endcard