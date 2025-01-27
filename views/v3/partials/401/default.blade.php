<div class="t-401">
    @typography(["element" => "h1", "id" => "header401", "classList" => ["c-typhography--401-heading"]])
        {{ $heading }}
    @endtypography

    @form([
        'action' => esc_url(site_url('wp-login.php', 'login_post'))
    ])
        @php 
            do_action('login_form');
        @endphp
        <input type="text" name="log" id="user_login" required>
        <input type="password" name="pwd" id="user_pass" required>
        <input type="hidden" name="redirect_to" value="https://localhost:60330/news/test/">
        <input type="submit" name="wp-submit" value="Log In">
    @endform

    <div class="t-401__buttons">
        @foreach($actionButtons as $button) 
            @button([
                'text' => $button->label,
                'href' => $button->href,
                'color' => $button->color,
                'style' => $button->style,
                'classList' => [
                    'u-margin__right--2', 
                    'u-margin__bottom--2', 
                    'u-margin__right--2', 
                    'u-display--block@xs'
                ],
                'size' => 'lg',
                'icon' => $button->icon,
                'reversePositions' => true,
            ])
            @endbutton
        @endforeach
    </div>
</div>