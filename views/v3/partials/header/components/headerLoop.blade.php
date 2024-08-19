<div class="c-header__{{$key}}-{{$align}}">
    @if(!empty($headerData[$key][$align]))
        @foreach($headerData[$key][$align] as $name => $classes)
            <div class="{{implode(' ', $classes)}}">
                @includeIf('partials.header.components.' . $name)
            </div>
        @endforeach
    @endif
</div>