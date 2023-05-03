@php
    echo '<pre>' . print_r('---- @openStreetMap here ----', true) . '</pre>';
@endphp
@if ($posts)
    @foreach ($posts as $post)
        @include('partials.openstreetmap.partials.post')
    @endforeach
@endif
{{-- @openStreetMap([
    'pins' => $pins,
    'startPosition' => $startPosition,
    'mapStyle' => $mapStyle
])
@endopenStreetMap --}}
