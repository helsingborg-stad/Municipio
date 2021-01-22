@php $accessabilityItems = apply_filters('accessibility_items', array()); @endphp
@if (!empty($accessabilityItems) && is_array($accessabilityItems))
	<ul class="nav-accessibility nav-horizontal u-print-display--none
				 unlist u-display--none@xs u-display--none@sm u-print-display--none"
                role="menubar">
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
