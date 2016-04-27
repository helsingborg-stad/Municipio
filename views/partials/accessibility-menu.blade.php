@if (function_exists('ReadSpeakerHelper_playButton'))
<ul class="nav nav-pills nav-horizontal gutter gutter-bottom">
    @if (function_exists('ReadSpeakerHelper_playButton'))
    <li>
        {!! ReadSpeakerHelper_playButton() !!}
    </li>
    @endif
    <li>
        <a href="#" onclick="window.print();return false;" class=""><i class="fa fa-print"></i> Skriv ut</a>
    </li>
</ul>
@endif

@if (function_exists('ReadSpeakerHelper_player'))
    {!! ReadSpeakerHelper_player() !!}
@endif
