@card([
    'classList' => [
        'kulturkortet-profile-editor',
        'o-layout-grid',
    ],
])
    @slot('aboveContent')
        @include('partials.title')
        @form([
            'method' => 'POST',
            'action' => $saveUrl,
            'classList' => [
                'kulturkortet-profile-editor__form',
                'o-layout-grid',
                'o-layout-grid--gap-6',
                'u-margin__top--4'
            ],
        ])
            @include('partials.fields')
            @include('partials.actions')
        @endform
    @endslot
@endcard
{{-- 
        @card([
            'color' => 'info',
            'heading' => 'Debug info - Vitec user data',
        ])
            @slot('content')
                <pre><code>{{ var_export($vitecUser, true) }}</code></pre>
            @endslot
        @endcard --}}