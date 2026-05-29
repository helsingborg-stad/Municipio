<form action="{{ $saveUrl }}" method="POST">
    @card([
                'color' => 'info',
                'heading' => $lang['heading'],
                'subHeading' => $profile['firstname'] . ' ' . $profile['lastname'],
                'content' => $lang['content'],
                'buttons' => [
                    ['type'=> 'submit','style' => 'filled', 'color' => 'primary', 'text' => $lang['saveUrl']],
                    ...(empty($attributes['ticketLinkLabel']) || empty($attributes['ticketLink']) ? [] : [['type' => 'filled', 'color' => 'primary', 'text' => $attributes['ticketLinkLabel'] ?? '', 'href' => $attributes['ticketLink'] ?? '']]),                
                    ['style' => 'filled', 'color' => 'secondary', 'text' => $lang['logoutUrl'], 'href' => $logoutUrl]
                ]
            ])
            @slot('belowContent')
            <div class="grid">
                <input type="hidden" name="action" value="save" />
                <div class="grid-md-6">
                    @field([
                        'type' => 'text',
                        'name' => 'firstname',
                        'value' => $profile['firstname'],
                        'label' => $lang['firstnameLabel'],
                        'placeholder' => $lang['firstnamePlaceholder'],
                        'fieldAttributeList' => ['readonly' => 'readonly', 'disabled' => 'disabled'],
                    ])
                    @endfield
                    @field([
                        'type' => 'text',
                        'name' => 'lastname',
                        'value' => $profile['lastname'],
                        'label' => $lang['lastnameLabel'],
                        'placeholder' => $lang['lastnamePlaceholder'],
                        'fieldAttributeList' => ['readonly' => 'readonly', 'disabled' => 'disabled'],
                    ])
                    @endfield
                    @field([
                        'type' => 'email',
                        'name' => 'email',
                        'value' => $profile['email'],
                        'label' => $lang['emailLabel'],
                        'placeholder' => $lang['emailPlaceholder'],
                        'fieldAttributeList' => ['autofocus' => 'autofocus'],
                        'required' => true
                    ])
                    @endfield
                </div>
            </div>
            @endSlot
    @endcard
    @if ($showDebugInfo)
        @card([
            'color' => 'info',
            'heading' => 'Debug info - Vitec user data',
        ])
            @slot('content')
                <pre><code>{{ var_export($vitecUser, true) }}</code></pre>
            @endslot
        @endcard
    @endif
</form>