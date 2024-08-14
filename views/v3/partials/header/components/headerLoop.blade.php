<div class="c-header__{{$row}}-{{$align}}">
    @if(!empty($headerData[$row][$align]))
        @foreach($headerData[$row][$align] as $name => $classes)
            <div class="{{implode(' ', $classes)}}">
                @includeIf('partials.header.components.' . $name)
            </div>
        @endforeach
    @endif
</div>