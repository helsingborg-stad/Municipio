@php $accessabilityItems = apply_filters('accessibility_items', array()); @endphp
@if (!empty($accessabilityItems) && is_array($accessabilityItems))
	<ul id="accessability-items" class="nav-accessibility nav-horizontal u-print-display--none
				 unlist u-display--none@xs u-display--none@sm u-print-display--none"
                role="menubar">
		@foreach ($accessabilityItems as $item) {{-- TODO: Move to controller. --}}
			<li role="menuitem">
				@link([
					'href' => $item['href'] ?? null,
					'attributeList' => [
						'onClick' => $item['script'] ?? '',
						'aria-label' => $item['label'] ?? '',
					]
				])
					@icon([
						'icon' => $item['icon'],
						'size' => 'md'
					])
					@endicon
					{{$item['text']}}
				@endbutton
			</li>
		@endforeach
	</ul>
@endif
