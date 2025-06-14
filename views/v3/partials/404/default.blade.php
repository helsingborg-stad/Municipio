<div class="{{ $wrapperClasses }}">
    @if ($image && !empty($image))
        <div class="t-404__image">
            @image([
                'src' => $image,
                'alt' => $heading,
                'classList' => ['c-image--404']
            ])
            @endimage
        </div>
    @endif

    <div class="content">
        @typography(["element" => "h1", "id" => "header404", "classList" => ["c-typhography--404-heading"]])
            {{ $heading }}
        @endtypography

        @if ($subheading && !empty($subheading))
            @typography(["classList" => ["c-typhography--404-subheading"]])
                {{ $subheading }}
            @endtypography
        @endif
    </div>

    <div class="t-404__buttons u-margin__top--4">
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