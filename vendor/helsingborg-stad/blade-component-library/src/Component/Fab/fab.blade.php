<span id="{{ $id }}" class="{{ $class }}">
    @if (isset($button))
        @button($button)
        @endbutton
    @else
        {!!$slot!!}
    @endif

</span>
