<div class="t-404">
    @typography(["element" => "h1", "id" => "header404", "classList" => ["c-typhography--404-heading"]])
        {{ $heading }}
    @endtypography

    @typography(["element" => "span", "id" => "content404", "classList" => ["c-typhography--404-subheading"]])
        {{ $subheading }}
    @endtypography

    <div class="t-404__buttons">
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