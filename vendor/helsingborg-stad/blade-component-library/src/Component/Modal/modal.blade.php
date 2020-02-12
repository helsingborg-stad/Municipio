<!-- modal.blade.php -->
<div id="{{ $id }}" class="{{ $parentClass }}">
    <div class="{{$class}}"  {!! $attribute !!}>
        <header class="{{$baseClass}}__header">
            @if ($heading)
                @typography([
                    "variant" => "h2",
                    "element" => "h2",
                ])
                 {{$heading}}
                @endtypography
            @endif
            
            @button([
                'type' => 'basic',
                'href' => '#btn-3',
                'type' => 'basic',
                'icon' => 'close',
                'size' => 'lg',
                'color' => 'secondary',
                'attributeList' => ['data-close' => ''],
                'classList' => [$baseClass . "__close"],
                'hasRipple' => true
            ])
            @endbutton
        </header>

        <section class="{{$baseClass}}__content">

            {{-- Previous button --}}
            @if ($navigation)
                @button([
                    'type' => 'basic',
                    'href' => '#previous',
                    'isOutlined' => false,
                    'isIconButton' =>  true,
                    'icon' => 'chevron_left',
                    'reverseIcon' => false,
                    'size' => 'md',
                    'color' => 'secondary',
                    'floating' => ['animate' => true, 'hover' => true],
                    'attributeList' => ['data-prev' => ''],
                    'classList' => [$baseClass . "__prev"],
                    'label' => 'Slide to previous'
                ])
                @endbutton
            @endif

            {!! $top !!}
            {{$slot}}

            {{-- Next button --}}
            @if ($navigation)
                @button([
                    'type' => 'basic',
                    'href' => '#next',
                    'isOutlined' => false,
                    'isIconButton' =>  true,
                    'icon' => 'chevron_right',
                    'reverseIcon' => false,
                    'size' => 'md',
                    'color' => 'secondary',
                    'floating' => ['animate' => true, 'hover' => true],
                    'attributeList' => ['data-next' => ''],
                    'classList' => [$baseClass . "__next"],
                    'label' => 'Slide to next'

                ])
                @endbutton
            @endif
        </section>

        <footer class="{{$baseClass}}__footer">
            {!! $bottom !!}
        </footer>
        @if ($navigation)

            @steppers(
            [
                'type' => 'dots'
            ])
            @endsteppers

        @endif
    </div>
</div>
