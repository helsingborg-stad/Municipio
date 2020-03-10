<span id="{{ $id }}" class="{{ $class }}">
    @if ($button)
        @button($button)
        @endbutton
    @else
        {!!$slot!!}
    @endif

</span>
