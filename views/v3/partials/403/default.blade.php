<div class="t-403">
    @if ($image && !empty($image))
        <div class="t-403__image">
            @image([
                'src' => $image,
                'alt' => $heading,
                'classList' => ['c-image--403']
            ])
            @endimage
        </div>
    @endif

    @typography(["element" => "h1", "id" => "header403", "classList" => ["c-typhography--403-heading"]])
        {{ $heading }}
    @endtypography

    @if ($subheading && !empty($subheading))
        @typography(["classList" => ["c-typhography--403-subheading"]])
            {{ $subheading }}
        @endtypography
    @endif

    <div class="t-403__buttons">
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