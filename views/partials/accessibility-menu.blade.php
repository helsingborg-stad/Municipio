<ul class="nav nav-pills nav-horizontal hidden-print">
    @if (function_exists('ReadSpeakerHelper_playButton'))
    <li>
        {!! ReadSpeakerHelper_playButton() !!}
    </li>
    @endif
    <li>
        <a href="#" onclick="window.print();return false;" class=""><i class="pricon pricon-print"></i> Skriv ut</a>
    </li>
</ul>

@if (function_exists('ReadSpeakerHelper_player'))
    {!! ReadSpeakerHelper_player() !!}
@endif
