<ul class="nav nav-help nav-accessibility nav-horizontal hidden-print rs_skip rs_preserve">
	@if (function_exists('ReadSpeakerHelper_playButton'))
		<li>
        	{!! ReadSpeakerHelper_playButton() !!}
    	</li>
    @endif
	@foreach (apply_filters('accessibility_items', array()) as $item)
		<li>
			{!! $item !!}
		</li>
	@endforeach
</ul>

@if (function_exists('ReadSpeakerHelper_player'))
    {!! ReadSpeakerHelper_player() !!}
@endif
