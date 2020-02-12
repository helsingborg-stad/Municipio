<div id="{{ $id }}" class="{{$class}}" {{$container}} {!!$attribute!!}>
    {{$slot}}
    @if(isset($isSplitButton) && $isSplitButton)
        @dropdown([
        'items' => [ ['text' => 'cool'], ['text' => 'story'], ['text' => 'bro']],
        'direction' => 'bottom',
        'popup' => 'click'
        ])
            @button([
                'text' => 'Left',
                'isIconButton' => true,
                'icon' => ['size' => 'md', 'color' => 'black', 'name' => 'arrow-drop-down'],
                'size' => 'md',
                
            ])
            @endbutton
        @enddropdown
    @endif
</div>
