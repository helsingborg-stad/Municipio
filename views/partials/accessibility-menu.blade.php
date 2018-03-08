<ul class="nav nav-help nav-accessibility nav-horizontal hidden-print rs_skip rs_preserve">
	@if (function_exists('ReadSpeakerHelper_playButton') && (is_single() ||is_page()))
		<li>
        	{!! ReadSpeakerHelper_playButton() !!}
    	</li>
    @endif
	@foreach (apply_filters('accessibility_items', array()) as $item)
		<li>
			{!! $item !!}
		</li>
	@endforeach
	@if (is_single() && !empty(apply_filters('Municipio/blog/post_settings', array(), $post)))
	<li>
	  	<span class="dropdown">
		    <i class="pricon pricon-menu-dots dropdown-toggle"></i>
		    <ul class="dropdown-menu">
		    	@foreach (apply_filters('Municipio/blog/post_settings', array(), $post) as $item)
		    	<li>
					{!! $item !!}
				</li>
		    	@endforeach
		    </ul>
		</span>
	</li>
	@endif
</ul>

@if (function_exists('ReadSpeakerHelper_player') && (is_single() ||is_page()))
    {!! ReadSpeakerHelper_player() !!}
@endif
