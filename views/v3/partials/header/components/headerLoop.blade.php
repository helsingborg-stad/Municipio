<div class="c-header__{{$area}}-{{$align}}">
    @if(!empty($headerData[$key][$align]))
        @foreach($headerData[$key][$align] as $name => $classes)
            @element([
                'classList' => [...$classes, 'u-align-items--center', 'c-header__item', 'c-header__item--' . $name]
            ])
                @includeIf('partials.header.components.' . $name)
            @endelement
        @endforeach
    @endif
</div>