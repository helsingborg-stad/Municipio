<form action="{{ $saveUrl }}" method="POST">
@card([
            'color' => 'info',
            'heading' => $lang['heading'],
            'subHeading' => $profile['name'],
            'content' => $lang['content'],
            'buttons' => [
                ['type'=> 'submit','style' => 'filled', 'color' => 'primary', 'text' => $lang['saveUrl']],
                ['style' => 'filled', 'color' => 'secondary', 'text' => $lang['logoutUrl'], 'href' => $logoutUrl]
            ]
        ])
        @slot('belowContent')
        <div class="grid">
            <input type="hidden" name="action" value="save" />
            <div class="grid-md-6">
                @field([
                    'type' => 'email',
                    'name' => 'email',
                    'value' => $profile['email'],
                    'label' => $lang['emailLabel'],
                    'placeholder' => $lang['emailPlaceholder'],
                    'required' => true
                ])
                @endfield
            </div>

            <pre><code>{{ var_export($vitecUser, true) }}</code></pre>
        </div>
        @endSlot
@endcard
</form>