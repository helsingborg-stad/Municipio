@if (empty($hideTitle) && !empty($postTitle))
    @typography([
        'id' => 'mod-text-' . $ID . '-label',
        'element' => 'h2',
        'variant' => 'h2',
        'classList' => ['module-title']
    ])
        {!! $postTitle !!}
    @endtypography
@endif

@element([
    'classList' => [
        'o-layout-grid',
        'o-layout-grid--cq',
        'o-layout-grid--cols-12',
        'o-layout-grid--gap-4'
    ],
    'attributeList' => !$hideTitle && !empty($postTitle) ? [
        'aria-labelledby' => 'mod-text-' . $ID . '-label'
    ] : []
])
    @foreach ($contacts as $contact)
        @element([
            'classList' => $columnsSpan
        ])
            @person([
                'givenName' => $contact['first_name'] ?? '',
                'familyName' => $contact['last_name'] ?? false,
                'jobTitle' => $contact['work_title'] ?? false,
                'administrationUnit' => $contact['administration_unit'] ?? false,
                'email' => $contact['email'] ?? false,
                'telephone' => $contact['phone'] ?? false,
                'address' => $contact['address'] ?? false,
                'visitingAddress' => $contact['visiting_address'] ?? false,
                'socialMedia' => $contact['social_media'] ?? false,
                'image' => $contact['thumbnail'] ?? false,
                'description' => $contact['other'] ?? false,
                'customSections' => $contact['custom_sections'] ?? [],
                'useAvatarFallback' => isset($placeholder_avatar) ? $placeholder_avatar : true,
                'view' => $view ?? 'extended',
                'classList' => $view === 'simple' ? [
                    'u-padding--1'
                ] : []
            ])
            @endperson
        @endelement
    @endforeach
@endelement