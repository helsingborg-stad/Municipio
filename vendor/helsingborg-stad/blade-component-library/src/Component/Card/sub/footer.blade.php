<div class="{{$baseClass}}__footer" js-toggle-class="u-display--none" js-toggle-item="{{$id}}">
    @if($buttons)
        @foreach($buttons as $button)
            @button($button)
            @endbutton
        @endforeach 
    @endif
</div>