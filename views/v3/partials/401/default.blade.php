<div class="t-401">
    @typography(["element" => "h1", "id" => "header401", "classList" => ["c-typhography--401-heading"]])
        {{ $heading }}
    @endtypography
@card([
        'classList' => ['u-level-2']
])
    @element([
        'classList' => ['c-card__body']
    ])
        @form([
            'action' => esc_url(site_url('wp-login.php', 'login_post'))
        ])
            @php 
                do_action('login_form');
            @endphp
            @field([
                'label' => $lang->usernameOrEmailAddress,
                'type' => 'text',
                'name' => 'log',
                'id' => 'user_login',
                'required' => true,
            ])
            @endfield
            @field([
                'label' => $lang->password,
                'type' => 'password',
                'name' => 'pwd',
                'id' => 'user_pass',
                'required' => true,
                'classList' => ['u-margin__top--2']
            ])
            @endfield
            @field([
                'type' => 'hidden',
                'name' => 'redirect_to',
                'value' => $currentUrl
            ])
            @endfield
            @button([
                'text' => $lang->login,
                'color' => 'secondary',
                'type' => 'submit',
                'attributeList' => [
                    'name' => 'wp-submit',
                ],
                'classList' => ['u-margin__top--3']
            ])
            @endbutton
        @endform
    @endelement
@endcard

    <div class="t-401__buttons u-margin__top--2">
        @foreach($actionButtons as $button) 
            @button([
                'text' => $button['label'],
                'href' => $button['href'],
                'color' => $button['color'],
                'style' => $button['style'],
                'classList' => [
                    'u-margin__right--2', 
                    'u-margin__bottom--2', 
                    'u-margin__right--2', 
                    'u-display--block@xs'
                ],
                'size' => 'lg',
                'icon' => $button['icon'],
                'reversePositions' => true,
            ])
            @endbutton
        @endforeach
    </div>
</div>