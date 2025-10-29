@if(!$helperNavBeforeContent)
    @include('partials.navigation.helper', [
        'classList' => $classes ?? ['screen-reader-text'],
    ])
@endif