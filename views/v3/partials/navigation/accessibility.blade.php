<ul class="nav-accessibility nav-horizontal hidden-print rs_skip rs_preserve">

	@if (function_exists('ReadSpeakerHelper_playButton') && (is_single() || is_page()))
		<li>
        	{!! ReadSpeakerHelper_playButton() !!}
    	</li>
    @endif

	@foreach (apply_filters('accessibility_items', array()) as $item)
		<li>

			@icon([
				'icon' => $item['icon'],
				'size' => 'md'
			])
			@endicon
			@link([
				'href' => $item['href'],
				'attributeList' => ['onClick' => $item['script']]

			])
				{{$item['text']}}
			@endbutton

		</li>
	@endforeach

</ul>

@if (function_exists('ReadSpeakerHelper_player') && (is_single() ||is_page()))
    {!! ReadSpeakerHelper_player() !!}
@endif
