@php $accessabilityItems = apply_filters('accessibility_items', array()); @endphp
@if (!empty($accessabilityItems) && is_array($accessabilityItems))
	<ul class="nav-accessibility nav-horizontal hidden-print rs_skip rs_preserve">
		@foreach ($accessabilityItems as $item) {{-- TODO: Move to controller. --}}
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
@endif
