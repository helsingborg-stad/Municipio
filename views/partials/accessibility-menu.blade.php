<ul class="nav nav-pills gutter gutter-bottom">
    @if (function_exists('ReadSpeakerHelper_playButton'))
    <li>
        {!! ReadSpeakerHelper_playButton() !!}
    </li>
    @endif
</ul>

@if (function_exists('ReadSpeakerHelper_player'))
    {!! ReadSpeakerHelper_player() !!}
@endif
